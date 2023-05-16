<?php

class Database {
	private $host = 'localhost';
	private $db_name = 'cms';
	private $username = 'root';
	private $password = 'root';
	private $conn = null;

	public function connect() {
		try {
			$this->conn = new PDO( 'mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password );
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch( PDOException $e ) {
			echo 'Connection Error: ' . $e->getMessage() . "\n";
			die();
		}

		return $this->conn;
	}
}
