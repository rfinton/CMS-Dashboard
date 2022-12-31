// Load the SearchForm
(function loadForm() {
	let territory = document.querySelector('#territory');
	let state = document.querySelector('#state');
	let region = document.querySelector('#region');
	let _clinics; // fetched clinics
	let _data;  // fetched menu data

	let load_territories = function() {
		if(document.querySelector('#territory')) {
			for(let d in _data[1]) {
				let opt = document.createElement('option');
				opt.value = _data[1][d].territory;
				opt.textContent = _data[1][d].territory;
				document.querySelector('#territory').appendChild(opt);
			}
		}	
	};

	let load_states = function() {
		if(document.querySelector('#state')) {
			let dedup_list = [];

			for(let d in _data[4]) {
				if( dedup_list.indexOf( _data[4][d].state_abbr ) == -1 ) {
					dedup_list.push(_data[4][d].state_abbr);
				}
			}

			dedup_list.sort();

			dedup_list.forEach(state => {
				let opt = document.createElement('option');
				opt.value = state;
				opt.textContent = state;
				document.querySelector('#state').appendChild(opt);
			});
		}
	};

	let load_regions = function() {
		if(document.querySelector('#region')) {
			for(let d in _data[0]) {
				let opt = document.createElement('option');
				opt.value = _data[0][d].region;
				opt.textContent = _data[0][d].region;
				document.querySelector('#region').appendChild(opt);
			}
		}
	};

	let load_default_menu = function() {
		if(!territory && !state && !region) {
			return;
		}
		territory.innerHTML = '<option value="">All</option>';
		state.innerHTML = '<option value="">All</option>';
		region.innerHTML = '<option value="">All</option>';
		load_territories();
		load_states();
		load_regions();
	};

	if(territory && state && region) {

		// Reset the form
		document.querySelector('#clear-btn').addEventListener('click', function(evt) {
			evt.preventDefault();
			window.location.reload();
		});

		// Update Territory and State when Region changes
		region.addEventListener('change', function() {

			if(this.value == '' && state.value != 'AZ') {
				load_default_menu();
				return;
			}

			if(state.value == 'AZ') {
				return;
			}

			let menu_updates = { states: [], territory: [] };

			_clinics.forEach(clinic => {
				if(clinic.region == region.value && menu_updates.states.indexOf(clinic.state_abbr) == -1) {
					menu_updates.states.push(clinic.state_abbr);
				}

				if(clinic.region == region.value && menu_updates.territory.indexOf(clinic.territory) == -1) {
					menu_updates.territory.push(clinic.territory);
				}
			});

			if(menu_updates.territory.length > 1) {
				territory.innerHTML = '<option value="">All</option>';
			} else {
				territory.innerHTML = '';
			}
			
			if(menu_updates.states.length > 1) {
				state.innerHTML = '<option value="">All</option>';
			} else {
				state.innerHTML = '';
			}

			menu_updates.states.forEach(_state => {
				let option = document.createElement('option');
				option.value = _state;
				option.textContent = _state;
				state.appendChild(option);
			});

			menu_updates.territory.forEach(_territory => {
				let option = document.createElement('option');
				option.value = _territory;
				option.textContent = _territory;
				territory.appendChild(option);
			});
		});

		// Update Territory and Region when State changes
		state.addEventListener('change', function() {

			if(this.value == '') {
				load_default_menu();
				return;
			}

			let results = _clinics.filter(clinic => clinic.state_abbr == state.value);
			let menu_updates = { regions: [], territory: []};
			
			results.forEach(res => {
				if(menu_updates.regions.indexOf(res.region) == -1)
					menu_updates.regions.push(res.region);
				if(menu_updates.territory.indexOf(res.territory) == -1)
					menu_updates.territory.push(res.territory);
			});

			if(menu_updates.regions.length > 1) {
				region.innerHTML = '<option value="">All</option>';

				menu_updates.regions.forEach(_region => {
					let o = document.createElement('option');
					o.setAttribute('value', _region);
					o.textContent = _region;
					region.appendChild(o);
				});
			} else {
				region.innerHTML = '<option value="' + menu_updates.regions[0] + '">' + menu_updates.regions[0] + '</option>';
			}

			territory.innerHTML = '<option value="' + menu_updates.territory[0] + '">' + menu_updates.territory[0] + '</option>';
		});

		// Update Region and State when Territory changes
		territory.addEventListener('change', function() {

			if(this.value == '') {
				load_default_menu();
				return;
			}

			let _states = _clinics.filter(clinic => clinic.territory == territory.value);
			
			let menu_updates = { states: [], regions: [] };
			let stateSelector = document.querySelector('#state');
			let regionSelector = document.querySelector('#region');
			stateSelector.innerHTML = '<option value="">All</option>';
			regionSelector.innerHTML = '<option value="">All</option>';
			
			for(let s in _states) {
				if(menu_updates.states.indexOf(_states[s].state_abbr) == -1) {
					var o = document.createElement('option');
					o.value = _states[s].state_abbr;
					o.textContent = _states[s].state_abbr;
					stateSelector.appendChild(o);
					menu_updates.states.push(_states[s].state_abbr);
				}

				if(menu_updates.regions.indexOf(_states[s].region) == -1) {
					var o = document.createElement('option');
					o.value = _states[s].region;
					o.textContent = _states[s].region;
					regionSelector.appendChild(o);
					menu_updates.regions.push(_states[s].region);
				}
			}
		});
	}

	fetch('/cms/api/menus/menus.php')
		.then(response => response.json())
		.then(data => {

			// Save clinics so I can use 'em later
			_clinics = data[4];
			_data = data;

			// Load territories, states, and regions
			load_default_menu();

			// Load Fiscal Year
			if(document.querySelector('#quarter')) {
				for(let d in data[5]) {
					let opt = document.createElement('option');
					opt.value = data[5][d].fiscal_quarter;
					opt.textContent = data[5][d].fiscal_quarter;
					document.querySelector('#quarter').appendChild(opt);
				}
			}

			// Load categories
			if(document.querySelector('#category')) {
				for(let d in data[2]) {
					let opt = document.createElement('option');
					opt.value = data[2][d].category;
					opt.textContent = data[2][d].category;
					document.querySelector('#category').appendChild(opt);
				}
			}

			// Load services
			if(document.querySelector('#service')) {
				let categories = {};

				// Create a service group map
				for(let d in data[3]) {
					if(categories[data[3][d].category]) {
						categories[data[3][d].category].push(data[3][d].service);
					} else {
						categories[data[3][d].category] = [data[3][d].service];
					}
				}

				// Load the map into the view
				for(let category in categories) {
					let optgroup = document.createElement('optgroup');
					optgroup.label = category;

					for(let i = 0; i < categories[category].length; i++) {
						let option = document.createElement('option');
						option.value = categories[category][i];
						option.textContent = categories[category][i];
						optgroup.appendChild(option);
					}

					document.querySelector('#service').appendChild(optgroup);
				}
			}

			// Load clinics
			if(document.querySelector('#clinic')) {
				let currentGroup, currentState, optgroup;
				
				let clinics = data[4].sort((a, b) => {
					if(a.territory === b.territory) {
						if(a.state_abbr === b.state_abbr) {
							return a.clinic < b.clinic ? -1 : 1;
						}
						return a.state < b.state ? -1 : 1;
					}
					
					return a.territory < b.territory ? -1 : 1;					
				});
				
				for(let d in clinics) {
					if(currentGroup != clinics[d].territory) {
						currentGroup = clinics[d].territory;
						optgroup = document.createElement('optgroup');
						optgroup.label = currentGroup + '_____________________';
						document.querySelector('#clinic').appendChild(optgroup);
					}
					
					if(currentState != clinics[d].state_abbr) {
        				currentState = clinics[d].state_abbr;
        				let separator = document.createElement('option');
        				separator.classList.add('separator');
        				separator.setAttribute('disabled', true);
        				separator.textContent = '____' + clinics[d].state_abbr + '____';
        				optgroup.appendChild(separator);
        			}
					
					let option = document.createElement('option');
					option.value = clinics[d].clinic;
					option.textContent = clinics[d].clinic;

					if(clinics[d].clinic == document.querySelector('#clinic').dataset.selectedClinic) {
						option.setAttribute('selected', '');
					}

					optgroup.appendChild(option);
				}
			}
		});
})();

(function handleNavMenu() {
	document.getElementById('bars').addEventListener('click', function(evt) {
		evt.preventDefault();
		document.querySelector('.nav-side').classList.add('show-side-menu');
	});

	document.querySelector('.nav-side .fa-times').addEventListener('click', function() {
		document.querySelector('.nav-side').classList.remove('show-side-menu');
	});
})();

function DonutChart(data, category, chartSize, labels) {
	this.canvas = new Canvas(chartSize.width, chartSize.height, category);
	this.data = {
		labels: labels || ['Clinics', 'None'],
		datasets: [{
			label: 'Dataset 1',
			data: data,
			backgroundColor: ['green', 'lightgray'],
		}],
	};
	this.config = {
		type: 'doughnut',
		data: this.data,
		width: 130,
		height: 130,
		options: {
			responsive: true,
			plugins: {
				legend: {
					position: '',
				 }
			  }
		},
	};
	new Chart(this.canvas, this.config);
}

function BarChartHz(_labels, _data, chartSize, range) {
	this.data = {
		labels: _labels,
		datasets: [{
			axis: 'y',
			label: '# of Clinics',
			data: _data,
			fill: false,
			backgroundColor: 'green',
			borderWidth: 1
		}]
	};

	this.config = {
		type: 'bar',
		data: this.data,
		options: {
			indexAxis: 'y',
			scales: {
				x: {
					max: range,
					ticks: {
						stepSize: 5,
					}
				},
				y: {
					ticks: {
						font: {
							size: 15
						}
					}
				}
			},
			plugins: {
				legend: {
					labels: {
						font: {
							size: 18
						}
					}
				}
			}
		},
	};

	this.canvas = new Canvas(chartSize.width, chartSize.height);
	new Chart(this.canvas, this.config);
}

function Canvas(width, height, category) {
	let div = document.createElement('div');
	div.style.width = width + 'px',
	div.style.height = 'auto';
	div.style.position = 'relative';

	let title = document.createElement('div');
	title.id = 'title';
	title.style.width = '100%';
	title.style.height = '40px';
	title.style.fontWeight = 'bold';
	title.style.textAlign = 'center';
	title.style.display = 'flex';
	title.style.flexDirection = 'column';
	title.style.justifyContent = 'center';
	title.textContent = category;

	let canvas = document.createElement('canvas');
	canvas.width = width;
	canvas.height = height;

	div.appendChild(title);
	div.appendChild(canvas);
	document.querySelector('.charts').appendChild(div);
	
	return canvas;
}

function callApi(url) {
	fetch(url, { cache: 'no-cache' })
		.then(response => response.json())
		.then(data => { 
			document.querySelector('#stats').innerHTML = '';
			document.querySelector('#clinics').innerHTML = '';
			let filterCount;

			// Display the Stats for Category Distribution
			if(location.pathname == '/cms/api/catdist/') {
				let territory = document.querySelector('#territory');
				let state = document.querySelector('#state');
				let region = document.querySelector('#region');

				if(state.value && region.value) {
					let filter = data[3].filter(clinic => {
						return (clinic.state_abbr == state.value) && (clinic.region == region.value);
					});
					filterCount = filter.length;
				}
				else if(territory.value && !region.value && !state.value) {
					let filter = data[3].filter(clinic => {
						return (clinic.territory == territory.value);
					});
					filterCount = filter.length;
				}
				else if(!territory.value && !state.value && !region.value) {
					filterCount = data[3].length;
				}
				else { // handle the AZ case
					let filter = data[3].filter(clinic => {
						return (clinic.state_abbr == state.value);
					});
					filterCount = filter.length;
				}

				data[0].map(item => {
					let p = document.createElement('p');
					p.textContent = item.category + ': ' + item.count;
					document.querySelector('#stats').appendChild(p);
				});

				document.querySelector('.charts').innerHTML = '';
				data[2].map(item => {
					let chartSize = {width: 130, height: 130};
					let result = data[0].filter(x => x.category == item.category);
					
					if(result.length > 0) {
						let dp = [result[0].count, filterCount - result[0].count];
						new DonutChart(dp, item.category, chartSize);
					} else {
						new DonutChart([0, filterCount], item.category, chartSize);
					}
				});
			}

			// Display the Stats for Service Distribution
			if(location.pathname == '/cms/api/servdist/') {
				data[0].map(item => {
					let p = document.createElement('p');
					p.textContent = item.service + ': ' + item.count;
					document.querySelector('#stats').appendChild(p);
				});

				let labels = [];
				let values = [];
				let range = data[3].length;
				let category = document.querySelector('#category').value;

				data[2].map(item => {	
					if(item.category == category && labels.indexOf(item.service) < 0) {

						// Create multi-line label for long names
						if(item.service.length > 34) {
							let x = item.service.split(' ');
							let multi_line_label = [x.slice(0, x.length/2).join(' '), x.slice(x.length/2).join(' ')];
							labels.push(multi_line_label);
						} else {
							labels.push(item.service);
						}

						let result = data[0].filter(x => x.service == item.service);
						
						if(result.length > 0) {
							values.push(result[0].count);
						} else {
							values.push(0);
						}
					}
				});
				
				document.querySelector('.charts').innerHTML = '';
				let chartSize = {width: 600, height: labels.length * 60};
				new BarChartHz(labels, values, chartSize, range);
			}

			// Display stats for Find Service
			if(location.pathname == '/cms/api/findserv/') {
				let territory = document.querySelector('#territory');
				let state = document.querySelector('#state');
				let region = document.querySelector('#region');
				let filterCount;

				data[0].map(item => {
					let p = document.createElement('p');
					p.textContent = item.service + ': ' + item.count;
					document.querySelector('#stats').appendChild(p);
				});

				if(state.value && region.value) {
					let filter = data[2].filter(clinic => {
						return (clinic.state_abbr == state.value) && (clinic.region == region.value);
					});
					filterCount = filter.length;
				}
				else if(territory.value && !region.value && !state.value) {
					let filter = data[2].filter(clinic => {
						return (clinic.territory == territory.value);
					});
					filterCount = filter.length;
				}
				else if(!territory.value && !state.value && !region.value) {
					filterCount = data[2].length;
				}
				else { // handle the AZ case
					let filter = data[2].filter(clinic => {
						return (clinic.state_abbr == state.value);
					});
					filterCount = filter.length;
				}

				document.querySelector('.charts').innerHTML = '';
				let chartSize = {width: 250, height: 250};
				let dp = [data[0][0].count, filterCount - data[0][0].count];
				new DonutChart(dp, dp[0] + ' / ' + filterCount, chartSize);
			}
			
			//Map the territories
			if(location.pathname != '/cms/api/offers/' && location.pathname != '/cms/api/submits/') {
				let states = {};

				data[1].map(item => {
					if(states[item.state_abbr]) {
						states[item.state_abbr].push(item.clinic);
					}
					else {
						states[item.state_abbr] = [item.clinic];
					}
				});

				// Loop through the territory map and display each clinic
				for(let t in states) {
					let heading = document.createElement('h3');
					heading.textContent = t;
					document.querySelector('#clinics').appendChild(heading);

					let ul = document.createElement('ul');
					states[t].sort();
					states[t].map(clinic => {
						let item = document.createElement('li');
						item.textContent = clinic;
						ul.appendChild(item);
					});
					document.querySelector('#clinics').appendChild(ul);
				}
			}

			// Display data for Clinic Offer
			if(location.pathname == '/cms/api/offers/') {
				// Show stats
				let total = 0;
				data[0].forEach(item => {
					total += parseInt(item.count);
				});

				let h3 = document.createElement('h3');
				h3.textContent = total + ' / ' + data[2].length;
				document.querySelector('#stats').appendChild(h3);

				document.querySelector('.charts').innerHTML = '';
				let chartSize = {width: 130, height: 130};
				let dp = [total, data[2].length - total];
				let category = (dp[0]/data[2].length*100).toFixed(1) + '% of all Services';
				let label = ['Services', 'None'];
				let firstDonut = new DonutChart(dp, category, chartSize, label);
				firstDonut.canvas.parentElement.style.gridColumn = '1/4';

				let categories = {};
				data[2].map(item => {
					let category = item.category;
					categories[category] = '';
				});

				for(let category in categories) {
					let results = data[1].filter(item => item.category == category);
					let services = data[2].filter(item => item.category == category);
					let dp = [results.length, services.length - results.length];
					new DonutChart(dp, category, chartSize, label);
				}

				data[0].map(item => {
					let p = document.createElement('p');
					p.textContent = item.category + ': ' + item.count;
					document.querySelector('#stats').appendChild(p);
				});

				// Show services available
				try {
					let currentHeading;
					for(let s in data[2]) {
						if(currentHeading != data[2][s].category) {
							currentHeading = data[2][s].category;
							let h3 = document.createElement('h3');
							h3.textContent = currentHeading;
							document.querySelector('#clinics').appendChild(h3);

							let ul = document.createElement('ul');
							document.querySelector('#clinics').appendChild(ul);
						}

						let li = document.createElement('li');
						li.textContent = data[2][s].service;
						li.style.color = 'lightgray';
						document.querySelector('#clinics ul:last-child').appendChild(li);

						data[1].forEach(function(entry) {
							if(entry.category == currentHeading && entry.service == data[2][s].service) {
								li.style.color = 'black';
								return;
							}
						});
					}
				} catch(e) {
					console.log(e);
				}
				
				// Update dropdown menu if referred from clinic menu
				if(location.search) {
					let clinic =  decodeURIComponent(location.search.substring(3));
					document.getElementById('clinic').value = clinic;
				}
			}

			// Display data for Submissions
			if(location.pathname == '/cms/api/submits/') {
				let clinicsWithData = data[0];
				let clinicsWithoutData = data[1];

				let territory = document.getElementById('territory');
				let state = document.getElementById('state');
				let region = document.getElementById('region');
				let filtered_submitters, filtered_nonsubmitters;

				if(territory.value && !state.value && !region.value) {
					filtered_submitters = clinicsWithData.filter(clinic => {
						return clinic.territory == territory.value;
					});
					filtered_nonsubmitters = clinicsWithoutData.filter(clinic => {
						return clinic.territory == territory.value;
					});
				}
				else if(state.value && region.value) {
					filtered_submitters = clinicsWithData.filter(clinic => {
						return (clinic.state_abbr == state.value) && (clinic.region == region.value);
					});
					filtered_nonsubmitters = clinicsWithoutData.filter(clinic => {
						return (clinic.state_abbr == state.value) && (clinic.region == region.value);
					});
				}
				else if(!territory.value && !state.value && !region.value) {
					filtered_submitters = clinicsWithData;
					filtered_nonsubmitters = clinicsWithoutData;
				}
				else { // Handle the AZ case (all az regions)
					filtered_submitters = clinicsWithData.filter(clinic => {
						return clinic.state_abbr == state.value;
					});
					filtered_nonsubmitters = clinicsWithoutData.filter(clinic => {
						return clinic.state_abbr == state.value;
					});
				}

				let current_state;
				document.querySelector('#clinics-with-data').innerHTML = '<h1>Clinics With Data</h1>';
				document.querySelector('#clinics-without-data').innerHTML = '<h1>Clinics Without Data</h1>';

				filtered_submitters.forEach(clinic => {
					if(current_state != clinic.state_abbr) {
						let div = document.createElement('div');
						let header = document.createElement('header');
						let ul = document.createElement('ul');
						let li = document.createElement('li');

						current_state = clinic.state_abbr;						
						header.textContent = current_state;
						li.textContent = clinic.clinic;

						div.appendChild(header);
						ul.appendChild(li);
						div.appendChild(ul)
						document.querySelector('#clinics-with-data').appendChild(div);
					} else {
						let ul = document.querySelector('#clinics-with-data div:last-child ul');
						let li = document.createElement('li');
						li.textContent = clinic.clinic;
						ul.appendChild(li);
					}
				});

				current_state = '';
				filtered_nonsubmitters.forEach(clinic => {
					if(current_state != clinic.state_abbr) {
						let div = document.createElement('div');
						let header = document.createElement('header');
						let ul = document.createElement('ul');
						let li = document.createElement('li');

						current_state = clinic.state_abbr;						
						header.textContent = current_state;
						li.textContent = clinic.clinic;

						div.appendChild(header);
						ul.appendChild(li);
						div.appendChild(ul)
						document.querySelector('#clinics-without-data').appendChild(div);
					} else {
						let ul = document.querySelector('#clinics-without-data div:last-child ul');
						let li = document.createElement('li');
						li.textContent = clinic.clinic;
						ul.appendChild(li);
					}
				});
			}
		});
}