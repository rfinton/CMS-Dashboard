<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include_once '../../config/Database.php';

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
	<title>CMS | Clinic Offer</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<link rel="stylesheet" href="/cms/assets/css/style.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
	<?php include '../menus/navigation.html'; ?>

	<div class="container" id="filter">
		<h1>Clinic Offer</h1>
		<p>Analysis of services offered at a specified clinic.</p>

		<form action="" class="row row-cols-lg-auto g-3 align-items-end">
			<div class="d-none">
				<label for="territory">Territory:</label>
				<select name="territory" id="territory" class="form-select">
					<option value="">All</option>
				</select>
			</div>
			
			<div class="d-none">
				<label for="state">State:</label>
				<select name="state" id="state" class="form-select">
					<option value="">All</option>
				</select>
			</div>

			<div class="d-none">
				<label for="region">Region:</label>
				<select name="region" id="region" class="form-select">
					<option value="">All</option>
				</select>
			</div>

			<div>
				<label for="clinic">Clinics:</label>
				<select name="clinic" id="clinic" class="form-select" required>
					<option value="">-Select Clinic-</option>
				</select>
			</div>

			<div>
				<label for="quarter">Fiscal Quarter:</label>
				<select name="quarter" id="quarter" class="form-select" required>
					<option value="">Pick</option>
				</select>
			</div>

			<div>
				<button type="submit" class="btn btn-primary">Submit</button>
				<a href="#" id="clear-btn" class="btn btn-secondary mx-3">Clear</a>
			</div>
		</form>
	</div>

	<div id="results-view">
		<div id="clinics"></div>
		<div class="charts" style="display: grid; gap: 2rem 3rem;"></div>
		<div id="stats"></div>
	</div>

	<script src="/cms/lib/utils.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
	        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
			  crossorigin="anonymous">
	</script>
	<script>
		let endpoint = '/cms/api/offers/offers.php';

		// Make api requests when user submits form
		document.forms[0].addEventListener('submit', function(ev) {
			ev.preventDefault();
			let clinic = document.querySelector('#clinic');
			let fq = document.querySelector('#quarter');

			let url = endpoint + '?clinic=' + encodeURIComponent(clinic.value) + '&fq=' + encodeURIComponent(fq.value);
			callApi(url);
		});
	</script>
</body>
</html>