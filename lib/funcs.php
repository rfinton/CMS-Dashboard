<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getUserIpAddr(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		 //ip from share internet
		 $ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		 //ip pass from proxy
		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		 $ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function getCurrentAndNextQuarter() {
	$year = date('Y');
	$current = strtotime(date('Y-m-d h:i'));
	$results = [];
	$quarters = [
		'Q1' => [
			'start' => strtotime("12:00am January 1 $year"), 
			'end' => strtotime("11:59pm March 31 $year")
		],
		'Q2' => [
			'start' => strtotime("12:00am April 1 $year"), 
			'end' => strtotime("11:59pm June 30 $year")
		],
		'Q3' => [
			'start' => strtotime("12:00am July 1 $year"), 
			'end' => strtotime("11:59pm September 30 $year")
		],
		'Q4' => [
			'start' => strtotime("12:00am October 1 $year"), 
			'end' => strtotime("11:59pm December 31 $year")
		]
	];

	foreach($quarters as $k => $v) {
		if( $k == 'Q4' && count($results) == 0 ) {
			$results = ["Q4 $year", "Q1 " . (intval($year) + 1)];
			break;
		}

		if( count($results) > 0 ) {
			array_push($results, "$k $year");
		}

		if( $current >= $v['start'] && $current <= $v['end'] ) {
			$results[0] = $k . " $year";
		}
	}

	return $results;
}

function getClinicServices( $_clinic, $fq ) {
	$database = new Database();
	$conn = $database->connect();

	$query = "SELECT 
					service_chart.ID,
					category,
					service,
					clinic,
					available,
					fiscal_quarter 
				FROM service_chart 
				LEFT JOIN services 
					ON service_chart.service_fk=services.ID 
				LEFT JOIN clinics 
					ON service_chart.clinic_fk=clinics.ID 
				LEFT JOIN service_group 
					ON services.group_fk=service_group.ID 
				WHERE clinics.clinic=:_clinic AND fiscal_quarter=:_fq
				ORDER BY category;";
	$stmt = $conn->prepare( $query );
	$stmt->execute([':_clinic' => $_clinic, ':_fq' => $fq]);
	$data = $stmt->fetchAll( PDO::FETCH_ASSOC );

	$service_array = [];

	foreach ( $data as $row ) {
		$service = new ServiceFormControl( $row );

		if ( !array_key_exists( $service->Category, $service_array ) ) {
			$service_array[$service->Category] = [$service];
		} else {
			array_push( $service_array[$service->Category], $service );
		}
	}

	return $service_array;
}
