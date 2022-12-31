<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config/Database.php';

/**
 * Function to create an array of chars
 * to use for auto-generating a password
 * uses 8 characters for password
 */
function generateCharArray()
{
	$chars = [];
	$meta_chars = "!@#$%^&*()";

	// ascii digits 0-9
	for($i = 48; $i < 58; $i++) {
		array_push($chars, chr($i));
	}

	// ascii chars A-Z
	for($i = 65; $i < 91; $i++) {
		array_push($chars, chr($i));
	}

	// ascii chars a-z
	for($i = 97; $i < 123; $i++) {
		array_push($chars, chr($i));
	}

	// append meta chars
	for($i = 0; $i < strlen($meta_chars); $i++) {
		array_push($chars, $meta_chars[$i]);
	}

	return $chars;
}

/**
 * Function to imports users from a CSV file.
 * Note: There is no dedupe process and
 * passwords are created and hashed
 */
function insertUsers($file) {
	// $chars = generateCharArray();

	$handle = fopen($file, 'r');
	$headers = fgetcsv($handle, 1000, ",");
	$index_cfk = array_search('clinic_fk', $headers);
	$index_ip4 = array_search('ipaddress', $headers);
	$index_usr = array_search('username', $headers);
	$index_pwd = array_search('password', $headers);
	
	$database = new Database();
	$conn = $database->connect();

	$rows = [];

	while ($data = fgetcsv($handle, 1000, ",")) {
		// shuffle($chars);
		// $password = substr(implode('', $chars), 0, 9);
		$data[$index_pwd] = password_hash($data[$index_pwd], PASSWORD_DEFAULT);
		array_push( $rows, $data );
	}

	foreach( $rows as $row ) {
		$cfk = $row[$index_cfk];
		$ip4 = $row[$index_ip4];
		$usr = $row[$index_usr];
		$pwd = $row[$index_pwd];
		$query = "INSERT INTO users (clinic_fk,ip4,username,password,email) VALUES ($cfk, '$ip4', '$usr', '$pwd', '')";
		$stmt = $conn->prepare( $query );
		$stmt->execute();
	}

	fclose($handle);
}

$users = 'users_pwd_plain.csv';
insertUsers($users);