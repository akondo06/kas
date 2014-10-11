<?php

class DBType {
	private $connection = null;
	
	public function __construct($host, $port, $username, $password, $database) {
		$this->connection = @mysql_connect($host.":".$port, $username, $password);
		if(!$this->connection) {
			exit("Database connection failed: ".mysql_error());
		} else { $db_select = @mysql_select_db($database, $this->connection);
			if(!$db_select) {
				exit("Database selection failed: ".mysql_error());
			} 
			$this->query("SET NAMES utf8");
			$this->query("SET CHARACTER SET utf8");
			$this->query("SET COLLATION_CONNECTION=\"utf8_general_ci\"");
		}
	}
	
	public function query($query=null) {
		$exec = mysql_query($query, $this->connection) or die(mysql_error());
		return $this->result($exec);
	}
	
	public function disconnect() {
		if($this->connection !== null) {
			mysql_close($this->connection);
			$this->connection = null;
			return true;
		}
		return false;
	}
	
	public function secure($data) {
		return mysql_real_escape_string($data);
	}
	
	public function result($query_result, $class_name="stdClass", array $params=array()) {
		if(is_resource($query_result) && stristr(get_resource_type($query_result), "mysql")) {
			$return = array();
			while($row = ($class_name == "stdClass") ? mysql_fetch_object($query_result) : mysql_fetch_object($query_result, $class_name, $params)) {
				$return[] = $row;
			}
			return $return;
		}
		return false;
	}

	public function affected_rows() {
		$conn = $this->connection;
		return mysql_affected_rows($conn);
	}
}

?>