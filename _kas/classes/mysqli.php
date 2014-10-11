<?php

class DBType {
	private $connection = null;
	
	public function __construct($host, $port, $username, $password, $database) {
		$this->connection = new mysqli($host, $username, $password, $database, $port);
		$c = $this->connection;
		if($c->connect_errno) {
			exit("Database connection failed: ".$c->connect_error);
		} else {
			$c->query("SET NAMES utf8");
			$c->query("SET CHARACTER SET utf8");
			$c->query("SET COLLATION_CONNECTION=\"utf8_general_ci\"");
		}
	}
	
	public function query($query=null) {
		$c = $this->connection;
		$exec = $c->query($query);
		if(!$exec) {
			exit($c->error);
		}
		return $this->result($exec);
	}
	
	public function disconnect() {
		if($this->connection !== null) {
			$this->connection->close();
			$this->connection = null;
			return true;
		}
		return false;
	}
	
	public function secure($data) {
		$c = $this->connection;
		return $c->real_escape_string($data);
	}
	
	public function result($query_result, $class_name="stdClass", array $params=array()) {
		if($query_result != null && is_a($query_result, "mysqli_result")) {
			$return = array();
			while($row = ($class_name == "stdClass") ? mysqli_fetch_object($query_result) : mysqli_fetch_object($query_result, $class_name, $params)) {
				$return[] = $row;
			}
			return $return;
		}
		return false;
	}

	public function affected_rows() {
		$conn = $this->connection;
		return $conn->affected_rows;
	}
}

?>