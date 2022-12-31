<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config/Database.php';

$database = new Database();
$conn = $database->connect();

$clinic_fk = '2';
$ip4 = '';
$username = 'cms_admin';
$password = password_hash('Cm$2022!', PASSWORD_DEFAULT);
$email = '';

$query = "INSERT INTO users (clinic_fk,ip4,username,password,email) VALUES ($clinic_fk, '$ip4', '$username', '$password', '$email')";
$stmt = $conn->prepare( $query );
$stmt->execute();