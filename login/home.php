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
	<title>CMS | Home</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body id="home-page">
	<?php include '../api/menus/navigation.html'; ?>

	<div id="main-menu">
		<a href="/cms/api/catdist">
			<i class="fa fa-pie-chart" aria-hidden="true"></i>
			Category View</a>
		
		<a href="/cms/api/servdist">
			<i class="fa fa-bar-chart" aria-hidden="true"></i>
			Service View</a>
			
		<a href="/cms/api/findserv">
			<i class="fa fa-search" aria-hidden="true"></i>
			Find A Service</a>
		
		<a href="/cms/api/update" id="update">
			<i class="fa fa-refresh" aria-hidden="true"></i>
			Update your<br>Clinic Menu</a>

		<a href="/cms/api/offers">
			<i class="fa fa-area-chart" aria-hidden="true"></i>
			Clinic View</a>

		<a href="/cms/api/submits">
		<i class="fa fa-check-square-o" aria-hidden="true"></i>
			Data Submissions</a>
	</div>

	<script src="../lib/utils.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
		crossorigin="anonymous">
	</script>
</body>
</html>