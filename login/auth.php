<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if( $_SERVER['REQUEST_METHOD'] === 'GET') {
	header('Location: /cms/login');
}

include_once '../config/Database.php';
include_once '../login/cms_admins.php';

session_start();
$database = new Database();
$conn = $database->connect();

function getUserIpAddr()
{
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
		 //ip from share internet
		 $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		 //ip pass from proxy
		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		 $ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function validatePassword($_conn, $_username, $_password) 
{
	$query_passwords = 'SELECT password FROM users WHERE username=:username;';
	$stmt = $_conn->prepare( $query_passwords );
	$stmt->execute([':username' => $_username]);
	$passwords = $stmt->fetchAll( PDO::FETCH_COLUMN, 0 );

	foreach( $passwords as $password ) {
		if( password_verify( $_password, $password) ) {
			return true;
		}
	}

	return false;
}

function validateIPAddress($_conn, $_username, $_ip4) 
{
	global $admins;
	if( in_array( $_username, $admins ) ) {
		return true;
	}

	$query_ip4 = 'SELECT ip4 FROM users WHERE username=:username;';
	$stmt = $_conn->prepare( $query_ip4 );
	$stmt->execute([':username' => $_username]);
	$ipaddresses = $stmt->fetchAll( PDO::FETCH_COLUMN, 0 );

	foreach( $ipaddresses as $ipaddress ) {
		if( $_ip4 == $ipaddress ) {
			return true;
		}
	}
	return false;
}

$passwordIsValid = validatePassword($conn, $_POST['username'], $_POST['password']);
$ipAddressIsValid = validateIPAddress($conn, $_POST['username'], getUserIpAddr());

$stmt = $conn->prepare("INSERT INTO access_logs (user,pwd,ip4) VALUES (:user, :pwd, :ip4)");
$stmt->bindParam(':user', $_POST['username']);
$stmt->bindParam(':pwd', $_POST['password']);
$stmt->bindParam(':ip4', getUserIpAddr());
$stmt->execute();

if( $passwordIsValid && $ipAddressIsValid ) {
	session_regenerate_id();
	$_SESSION['login'] = TRUE;
	$_SESSION['name'] = $_POST['username'];
	$_SESSION['userid'] = $result['ID'];
	header('Location: /cms/login/home.php');
}
else {
	$_SESSION['name'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
	$_SESSION['login'] = 'failed';

	$authresult = "pwd:";
	$authresult .= $passwordIsValid ? 'true':'false';
	$authresult .= ",ip4:";
	$authresult .= $ipAddressIsValid ? 'true':'false';
	$_SESSION['authresult'] = $authresult;
	header('Location: /cms/login');
}