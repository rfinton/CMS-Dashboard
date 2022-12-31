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
	'fiscal_quarter' => ( $_GET['fq'] != '' ) ? $_GET['fq'] : 0
];

$database = new Database();
$db = $database->connect();

$cat = new Category( $db );
$set = $cat->get_categories();
$all_categories = $set->fetchAll( PDO::FETCH_ASSOC );

$cat_dist = new CategoryDistribution( $db );
$result = $cat_dist->fetchStats( $url_params );
$stats = $result->fetchAll( PDO::FETCH_ASSOC );

$result = $cat_dist->fetchClinics( $url_params );
$clinics = $result->fetchAll( PDO::FETCH_ASSOC );

// $clinic = new Clinic( $db );
// $set = $clinic->get_clinics();
// $all_clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$dataSubmissions = new DataSubmissions( $db );
$set = $dataSubmissions->fetchClinicsWithData( $url_params );
$clinics_with_data = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode( [$stats, $clinics, $all_categories, $clinics_with_data] );