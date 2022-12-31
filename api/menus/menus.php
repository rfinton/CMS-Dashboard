<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header( 'Access-Control-Allow-Origin: *' );
header( 'Content-Type: application/json' );

include_once '../../config/Database.php';
include_once '../../models/models.php';

$database = new Database();
$db = $database->connect();

$regions = new Region( $db );
$set = $regions->get_regions();
$_regions = $set->fetchAll( PDO::FETCH_ASSOC );

$territories = new Territory( $db );
$set = $territories->get_territories();
$_territories = $set->fetchAll( PDO::FETCH_ASSOC );

$categories = new Category( $db );
$set = $categories->get_categories();
$_categories = $set->fetchAll( PDO::FETCH_ASSOC );

$services = new Service( $db );
$set = $services->get_services();
$_services = $set->fetchAll( PDO::FETCH_ASSOC );

$clinics = new Clinic( $db );
$set = $clinics->get_clinics();
$_clinics = $set->fetchAll( PDO::FETCH_ASSOC );

$fiscal_quarters = new FiscalQuarter( $db );
$set = $fiscal_quarters->fetchFiscalQuarters();
$_fiscal_quarters = $set->fetchAll( PDO::FETCH_ASSOC );

echo json_encode([
	$_regions, 
	$_territories, 
	$_categories, 
	$_services, 
	$_clinics,
	$_fiscal_quarters
]);