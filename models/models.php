<?php

class Region {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function get_regions() {
		$query = 'SELECT * FROM regions ORDER BY region ASC;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class Territory {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function get_territories() {
		$query = 'SELECT region,territory FROM territories
					LEFT JOIN regions ON territories.region_fk=regions.ID
					ORDER BY region;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class Category {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function get_categories() {
		$query = 'SELECT * FROM service_group';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class Service {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function get_services() {
		$query = 'SELECT category,service FROM services 
					LEFT JOIN service_group ON services.group_fk=service_group.ID
					ORDER BY category;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class Clinic {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function get_clinics() {
		$query = 'SELECT territory,state_abbr,region,clinic FROM clinics
					LEFT JOIN territories ON clinics.territory_fk=territories.ID
					LEFT JOIN regions ON clinics.region_fk=regions.ID
					LEFT JOIN states ON clinics.state_fk=states.state_id
					ORDER BY territory, state_abbr, region, clinic;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class ServiceDistribution {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function fetchStats( $params ) {
		$var_query = 'SELECT service,count(DISTINCT clinic) AS count 
			FROM service_chart 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';

		if( $params['territory'] )
			$var_query .= 'AND territory=:territory ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';

		if( $params['category'] )
			$var_query .= 'AND service_group.category=:category ';

		$var_query .= 'AND fiscal_quarter=:fiscal_quarter GROUP BY services.service;';
		$stmt = $this->conn->prepare( $var_query );
		$bound_variables = [];

		foreach( $params as $key => $val) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}

		$stmt->execute( $bound_variables );

		return $stmt;
	}
	
	public function fetchClinics( $params ) {
		$var_query = 'SELECT DISTINCT territory,state_abbr,region,clinic FROM `service_chart` 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';

		if( $params['territory'] )
			$var_query .= 'AND territory=:territory ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';

		if( $params['category'] )
			$var_query .= 'AND category=:category ';
		
		$var_query .= 'AND service_chart.fiscal_quarter=:fiscal_quarter ORDER BY state_abbr;';

		$stmt = $this->conn->prepare( $var_query );

		$bound_variables = [];

		foreach( $params as $key => $val) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}
		
		$stmt->execute( $bound_variables );

		return $stmt;
	}
}

class CategoryDistribution {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function fetchStats( $params ) {
		$var_query = 'SELECT category, count(DISTINCT clinic) AS count 
			FROM service_chart 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';
		
		if( $params['territory'] ) 
			$var_query .= 'AND territory=:territory ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		$var_query .= 'AND fiscal_quarter=:fiscal_quarter GROUP BY category;';
		$stmt = $this->conn->prepare( $var_query );

		$bound_variables = [];

		foreach( $params as $key => $val ) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}

		$stmt->execute( $bound_variables );

		return $stmt;
	}
	
	public function fetchClinics( $params ) {
		$var_query = 'SELECT DISTINCT territory,state_abbr,region,clinic FROM `service_chart` 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';

		if( $params['territory'] )
			$var_query .= 'AND territory=:territory ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';
		
		$var_query .= 'AND service_chart.fiscal_quarter=:fiscal_quarter ORDER BY state_abbr;';

		$stmt = $this->conn->prepare( $var_query );

		$bound_variables = [];

		foreach( $params as $key => $val) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}
		
		$stmt->execute( $bound_variables );

		return $stmt;
	}
}

class FindService {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function fetchStats( $params ) {
		$var_query = 'SELECT service,count(DISTINCT clinic) AS count FROM service_chart 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';
		
		if( $params['territory'] ) 
			$var_query .= 'AND territory=:territory ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		if( $params['service'] )
			$var_query .= 'AND service=:service ';

		$var_query .= 'AND fiscal_quarter=:fiscal_quarter;';
		$stmt = $this->conn->prepare( $var_query );

		$bound_variables = [];

		foreach( $params as $key => $val ) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}

		$stmt->execute( $bound_variables );

		return $stmt;
	}

	public function fetchClinics( $params ) {
		$var_query = 'SELECT DISTINCT state_abbr,clinic FROM service_chart
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1 ';
		
		if( $params['territory'] ) 
			$var_query .= 'AND territory=:territory ';

		if( $params['region'] )
			$var_query .= 'AND region=:region ';

		if( $params['state_abbr'] )
			$var_query .= 'AND state_abbr=:state_abbr ';

		if( $params['service'] )
			$var_query .= 'AND service=:service ';

		$var_query .= 'AND fiscal_quarter=:fiscal_quarter ORDER BY state_abbr;';
		$stmt = $this->conn->prepare( $var_query );

		$bound_variables = [];

		foreach( $params as $key => $val ) {
			if($val)
				$bound_variables[':' . $key] = $val;
		}

		$stmt->execute( $bound_variables );

		return $stmt;
	}
}

class ClinicOffer {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function fetchStats( $params ) {
		$query = 'SELECT category,COUNT(service) AS count FROM service_chart
					LEFT JOIN services ON service_chart.service_fk=services.ID
					LEFT JOIN service_group ON services.group_fk=service_group.ID
					LEFT JOIN clinics ON service_chart.clinic_fk=clinics.ID
					WHERE service_chart.available=1 
					AND clinic=:clinic 
					AND fiscal_quarter=:fiscal_quarter 
					GROUP BY category;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute([
			':clinic' => $params['clinic'], 
			':fiscal_quarter' => $params['fq']
		]);
		return $stmt;
	}

	public function fetchServicesOffered( $params ) {
		$query = 'SELECT category,service FROM service_chart
					LEFT JOIN services ON service_chart.service_fk=services.ID
					LEFT JOIN service_group ON services.group_fk=service_group.ID
					LEFT JOIN clinics ON service_chart.clinic_fk=clinics.ID
					WHERE service_chart.available=1 
					AND clinic=:clinic 
					AND fiscal_quarter=:fiscal_quarter 
					ORDER BY category;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute([
			':clinic' => $params['clinic'],
			':fiscal_quarter' => $params['fq']
		]);
		return $stmt;
	}

	public function fetchAllServices() {
		$query = 'SELECT category,service FROM services
					LEFT JOIN service_group ON services.group_fk=service_group.ID
					ORDER BY category;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class FiscalQuarter {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}

	public function fetchFiscalQuarters() {
		$query = 'SELECT DISTINCT fiscal_quarter FROM service_chart;';
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}
}

class DataSubmissions {
	private $conn;

	public function __construct( $db ) {
		$this->conn = $db;
	}	

	public function fetchClinicsWithData( $params ) {
		$var_query = 'SELECT DISTINCT territory,state_abbr,region,clinic FROM `service_chart` 
			LEFT JOIN clinics ON clinic_fk=clinics.ID 
			LEFT JOIN services ON service_fk=services.ID 
			LEFT JOIN service_group ON group_fk=service_group.ID 
			LEFT JOIN territories ON territory_fk=territories.ID 
			LEFT JOIN regions ON clinics.region_fk=regions.ID 
			LEFT JOIN states ON state_fk=states.state_id 
			WHERE available=1
			AND fiscal_quarter=:fiscal_quarter
			ORDER BY state_abbr;';

		$stmt = $this->conn->prepare( $var_query );
		
		$stmt->execute([
			':fiscal_quarter' => $params['fiscal_quarter']
		]);

		return $stmt;
	}

	public function fetchClinicsWithNoData( $params ) {
		$var_query = 'SELECT territory,state_abbr,region,clinic FROM clinics
			LEFT JOIN territories ON territory_fk=territories.ID
			LEFT JOIN states ON state_fk=states.state_id
			LEFT JOIN regions ON clinics.region_fk=regions.ID
			WHERE clinics.ID NOT IN (
				SELECT DISTINCT clinic_fk FROM `service_chart` 
				LEFT JOIN clinics ON clinic_fk=clinics.ID 
				LEFT JOIN territories ON territory_fk=territories.ID 
				LEFT JOIN regions ON clinics.region_fk=regions.ID 
				LEFT JOIN states ON state_fk=states.state_id 
				WHERE available=1 
				AND fiscal_quarter=:fiscal_quarter
			)
			ORDER BY state_abbr;';

		$stmt = $this->conn->prepare( $var_query );
		$stmt->execute([
			':fiscal_quarter' => $params['fiscal_quarter']
		]);

		return $stmt;
	}
}