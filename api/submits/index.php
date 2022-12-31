<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if( !isset($_SESSION['login']) ) {
	header('Location: /cms/login/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS | Data Submissions</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<link rel="stylesheet" href="/cms/assets/css/style.css">
	<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
</head>
<body>
	<?php include '../menus/navigation.html'; ?>

	<div class="container" id="filter">
		<h1>Data Submissions</h1>
		<p>Report which clinics have submitted quarterly services data.</p>

		<form action="" class="row row-cols-lg-auto g-3 align-items-end">
			<div>
				<label for="territory">Territory:</label>
				<select name="territory" id="territory" class="form-select">
					<option value="">All</option>
				</select>
			</div>
			
			<div>
				<label for="state">State:</label>
				<select name="state" id="state" class="form-select">
					<option value="">All</option>
				</select>
			</div>

			<div>
				<label for="region">Region:</label>
				<select name="region" id="region" class="form-select">
					<option value="">All</option>
				</select>
			</div>

			<div>
				<label for="quarter">Fiscal Quarter:</label>
				<select name="quarter" id="quarter" class="form-select" required>
					<option value="">Pick</option>
				</select>
			</div>

			<button type="submit" class="btn btn-primary">Submit</button>
			<a href="#" id="clear-btn" class="btn btn-secondary mx-3">Clear</a>
		</form>
	</div>

	<div id="results-view" class="container">
		<div id="clinics" class="d-none"></div>
		<div class="charts" style="display: none;"></div>
		<div id="stats" class="d-none"></div>

		<div style="display: flex; width: 100%;">
			<div id="clinics-with-data" style="flex: 1;">
				<h1>Clinics With Data</h1>
			</div>
			<div id="clinics-without-data" style="flex: 1;">
				<h1>Clinics Without Data</h1>	
			</div>
		</div>
	</div>

	<script src="/cms/lib/utils.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
	        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
			  crossorigin="anonymous">
	</script>
	<script>
		const endpoint = '/cms/api/submits/submits.php';

		document.forms[0].addEventListener('submit', function(ev) {
			ev.preventDefault();
			let terr = encodeURIComponent(document.getElementById('territory').value);
			let state = encodeURIComponent(document.getElementById('state').value);
			let region = encodeURIComponent(document.getElementById('region').value);
			let fq = encodeURIComponent(document.getElementById('quarter').value);
			let url_query_string = '?terr=' + terr;
			url_query_string += '&state=' + state;
			url_query_string += '&region=' + region;
			url_query_string += '&fq=' + fq;
			callApi(endpoint + url_query_string);
		});
	</script>
</body>
</html>