<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../../config/Database.php';

$data = json_decode( file_get_contents('php://input'), true );
$status = ['msg' => ''];

if( $data ) {
	$db = new Database();
	$conn = $db->connect();
}

foreach( $data as $key => $value ) {
	if( $value == 'true' )
		$value = 1;
	
	if( $value == 'false' )
		$value = 0;
	
	if( $value == 1 || $value == 0 ) {
		$query = 'UPDATE service_chart SET available=:value WHERE ID=:key;';
		$stmt = $conn->prepare( $query );
		$stmt->execute([':value' => intval($value), ':key' => intval($key)]);
		$status['msg'] = 'success';
	} else {
		$status['msg'] = 'failed';
	}
}

echo json_encode( $status );