<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header( 'Access-Control-Allow-Origin: *' );
header( 'Content-Type: application/json' );

include_once '../../config/Database.php';
include_once '../../models/models.php';

$url_params = [
	'clinic' => ( $_GET['clinic'] != '' ) ? $_GET['clinic'] : 0,
	'fq' => ( $_GET['fq'] != '' ) ? $_GET['fq'] : 0
];

$database = new Database();
$db = $database->connect();

$offer = new ClinicOffer( $db );

$set = $offer->fetchStats( $url_params );
$stats = $set->fetchAll( PDO::FETCH_ASSOC );

$set = $offer->fetchServicesOffered( $url_params );
$clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$set = $offer->fetchAllServices();
$services = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode( [$stats, $clinics, $services] );