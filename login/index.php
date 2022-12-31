<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$username = '';
$password = '';
$class = '';

if(isset($_SESSION['login']) && ($_SESSION['login'] == 'failed')) {
	$class = 'show';
	$username = $_SESSION['name'];
	unset($_SESSION['login']);
	unset($_SESSION['name']);
	unset($_SESSION['password']);
	unset($_SESSION['authresult']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS | Dashboard</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<style>
		.container {
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
		form {
			width: 90%;
			max-width: 420px;
			margin: auto;
		}
		.invalid-login {
			color: red;
			font-weight: bold;
			padding-top: 1em;
			display: none;
		}
		.invalid-login.show {
			display: inline;
		}
	</style>
</head>
<body>
	<div class="container">
		<form action="auth.php" method="post">
			<div class="mb-3">
				<label for="username" class="form-label">User Name</label>
				<input type="text" name="username" class="form-control" id="username" aria-describedby="userName" value="<?php echo $username; ?>" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" name="password" class="form-control" id="password" value="" required>
			</div>
			<button type="submit" class="btn btn-primary">Login</button>
			<div class="invalid-login<?php if($class != '') echo ' ' . $class; ?>">
				Incorrect username and/or password.
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
		crossorigin="anonymous">
	</script>
</body>
</html>