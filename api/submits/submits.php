<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header( 'Access-Control-Allow-Origin: *' );
header( 'Content-Type: application/json' );

include_once '../../config/Database.php';
include_once '../../models/models.php';

$url_params = [
	'territory' => ( $_GET['terr'] != '' ) ? $_GET['terr'] : 0,
	'state_abbr' => ( $_GET['state'] != '' ) ? $_GET['state'] : 0,
	'region' => ( $_GET['region'] != '' ) ? $_GET['region'] : 0,
	'fiscal_quarter' => $_GET['fq']
];

$database = new Database();
$db = $database->connect();

$dataSubmissions = new DataSubmissions( $db );
$set = $dataSubmissions->fetchClinicsWithData( $url_params );
$clinics_with_data = $set->fetchAll( PDO::FETCH_ASSOC );

$set = $dataSubmissions->fetchClinicsWithNoData( $url_params );
$clinics_without_data = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode([
	$clinics_with_data,
	$clinics_without_data
]);