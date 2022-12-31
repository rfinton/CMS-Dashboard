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
	'category' => ( $_GET['cat'] != '' ) ? $_GET['cat'] : 0,
	'fiscal_quarter' => ( $_GET['fq'] != '' ) ? $_GET['fq'] : 0
];

$database = new Database();
$db = $database->connect();

$servdist = new ServiceDistribution( $db );

$set = $servdist->fetchStats( $url_params );
$stats = $set->fetchAll( PDO::FETCH_ASSOC );

$set = $servdist->fetchClinics( $url_params );
$clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$services = new Service( $db );
$set = $services->get_services();
$services = $set->fetchAll( PDO::FETCH_ASSOC );

// $clinic = new Clinic( $db );
// $set = $clinic->get_clinics();
// $all_clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$dataSubmissions = new DataSubmissions( $db );
$set = $dataSubmissions->fetchClinicsWithData( $url_params );
$clinics_with_data = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode( [ $stats, $clinics, $services, $clinics_with_data ] );