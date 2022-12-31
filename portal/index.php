<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if( ! $_SESSION['login'] ) {
	header( 'Location: /cms/login' );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS Dashboard Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<link rel="stylesheet" href="/cms/assets/css/style.css">
</head>
<body>
	<?php include '../api/menus/navigation.html'; ?>
	<div class="container" style="padding-top: 9em;">
		<h1>Change Password</h1>
		<form action="/cms/api/reset-pwd/index.php" method="post">
			<div class="form-group">
				<label for="username">User Name:</label>
				<input type="text" class="form-control" name="username" id="username" required>
			</div>
			<div class="form-group mt-5">
				<label for="password">New Password:</label>
				<input class="form-control" type="password" id="pass-1" required>
				<small>Enter new password again:</small>
				<input class="form-control" type="password" id="pass-2" required>
				<input type="hidden" class="form-control" name="password" id="password">
			</div>
			<input type="submit" value="Submit" class="btn btn-info mt-3">
		</form>
	</div>
	<script>
		document.forms[0].addEventListener('submit', function(evt) {
			evt.preventDefault();
			if(document.querySelector('#pass-1').value === document.querySelector('#pass-2').value) {
				document.querySelector('#password').value = document.querySelector('#pass-1').value;
				this.submit();
			} else {
				alert('Passwords do not match.');
				return false;
			}
		});	
	</script>
</body>
</html>