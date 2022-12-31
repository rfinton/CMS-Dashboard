<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if( isset( $_SESSION['login'] ) ) {
	include_once '../../config/Database.php';
	include_once '../../login/cms_admins.php';

	$database = new Database();
	$conn = $database->connect();

	$password = password_hash( $_POST['password'], PASSWORD_DEFAULT );

	$stmt = $conn->prepare( "UPDATE users SET password=:pwd WHERE username=:user;" );
	$stmt->bindParam( ':pwd', $password );
	$stmt->bindParam( ':user', $_POST['username'] );
	$stmt->execute();
}

header( 'Location: /cms/login' );