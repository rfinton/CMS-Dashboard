<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header( 'Access-Control-Allow-Origin: *' );
header( 'Content-Type: application/json' );

$url_params = [
	'territory' => ( $_GET['terr'] != '' ) ? $_GET['terr'] : 0,
	'state_abbr' => ( $_GET['state'] != '' ) ? $_GET['state'] : 0,
	'region' => ( $_GET['region'] != '' ) ? $_GET['region'] : 0,
	'service' => ( $_GET['service'] != '' ) ? $_GET['service'] : 0,
	'fiscal_quarter' => ( $_GET['fq'] != '' ) ? $_GET['fq'] : 0
];

include_once '../../config/Database.php';
include_once '../../models/models.php';

$database = new Database();
$db = $database->connect();

$findserv = new FindService( $db );
$set = $findserv->fetchStats( $url_params );
$stats = $set->fetchAll( PDO::FETCH_ASSOC );

$set = $findserv->fetchClinics( $url_params );
$clinics = $set->fetchAll( PDO::FETCH_ASSOC );

// $all_clinics = new Clinic( $db );
// $set = $all_clinics->get_clinics();
// $all_clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$dataSubmissions = new DataSubmissions( $db );
$set = $dataSubmissions->fetchClinicsWithData( $url_params );
$clinics_with_data = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode( [$stats, $clinics, $clinics_with_data] );