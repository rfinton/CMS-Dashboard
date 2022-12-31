<?php

include_once '../config/Database.php';

$clinics = ['59'];

$database = new Database();
$conn = $database->connect();

foreach($clinics as $clinic) {
	for($i = 1; $i < 85; $i++) {
		$query = "INSERT INTO service_chart (clinic_fk,service_fk,available,fiscal_quarter) VALUES ($clinic,$i,0,'Q4 2022');";
		$stmt = $conn->prepare( $query );
		$stmt->execute();
	}
}