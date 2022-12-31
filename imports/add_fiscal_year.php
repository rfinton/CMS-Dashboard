<?php

/**
 * This file is used to generate
 * table data for a new quarter
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config/Database.php';
$database = new Database();
$conn = $database->connect();

$fq = 'Q1 2023';

$query_services = "SELECT services.ID FROM services JOIN service_group ON group_fk=service_group.ID;";
$stmt_services = $conn->prepare($query_services);
$stmt_services->execute();
$result_services = $stmt_services->fetchAll( PDO::FETCH_ASSOC );

$query_clinics = "SELECT ID FROM clinics;";
$stmt_clinics = $conn->prepare($query_clinics);
$stmt_clinics->execute();
$result_clinics = $stmt_clinics->fetchAll( PDO::FETCH_ASSOC );

foreach ( $result_clinics as $clinic ) {
	foreach ( $result_services as $service ) {
		$insert = "INSERT INTO service_chart (available,clinic_fk,fiscal_quarter,service_fk) VALUES (?,?,?,?)";
		$stmt = $conn->prepare($insert);
		$stmt->execute([0, $clinic['ID'], $fq, $service['ID']]);
	}
}