<?php

class Database {
	private $type = null;
	private $type_name = null;
	private $last_query = null;
	private $queries_no = 0;
	
	private $cache_folder = null;
	private $cache = null;
	private $cache_time = 0;
	private $cached_queries_no = 0;
	
	private $result = null;
	
	public function __construct($type="mysqli", $cache_folder = "cache/") {
		$this->type_name = $type;
		$this->cache_folder = $cache_folder;
	}
	
	public function connect($host, $port, $username, $password, $database) {
		if($this->type_name != null) {
			require_once($this->type_name.".php");
			if(class_exists("DBType")) {
				$this->type = new DBType($host, $port, $username, $password, $database);
			} else {
				exit("Could not load the database type: ".$this->type_name);
			}
		}
		return false;
	}
	
	public function query($query=null, $cache=true) {
		$this->last_query = $query;
		$return = null;
		$current_type = $this->type;
		
		if($cache === true && $this->cache_time > 0) {
			if($this->cache == null) {
				if(!is_string($this->cache_folder)) { exit("INVALID_CACHE_FOLDER"); }
				$this->cache = new Cache($this->cache_folder);
			}
			$current_cache = $this->cache;
			$data = $current_cache->get($query);
			if($data === FALSE || $this->cache == null) {
				$data = $current_type->query($query);
				$current_cache->set($query, $data);
			} else {
				$this->cached_queries_no++;
			}
			$return = $data;
		} else {
			$return = $current_type->query($query);
		}
		$this->result = $return;
		$this->queries_no++;
		return $return;
	}
	
	public function result() {
		return $this->result;
	}
	
	public function cache_time($seconds=0) {
		if((int)$seconds == $seconds && (int)$seconds > 0) {
			$this->cache_time = $seconds;
			return true;
		}
		return $this->cache_time;
	}
	
	public function type() {
		return $this->type_name;
	}
	
	public function secure($data=null) {
		if($data !== null && !(is_bool($data) || is_numeric($data))) {
			if(!is_array($data) && is_object($data)) {
				$fields = get_object_vars($data);
				foreach($fields as $var => $value) {
					$data->$var = $this->secure($value);
				}
			} else if(is_array($data)) {
				foreach($data as $var => $value) {
					$data[$var] = $this->secure($value);
				}
			} else {
				$type = $this->type;
				return $type->secure($data);
			}
		}
		return $data;
	}
	
	public function clear() {
		//$this->type = null;
		$this->last_query = null;
		$this->queries_no = 0;
		$this->cache = null;
		$this->cache_time = 0;
		$this->cached_queries_no = 0;
		$this->result = null;
	}
	
	public function no_of_queries() {
		return $this->queries_no;
	}
	
	public function no_of_cached_queries() {
		return $this->cached_queries_no;
	}
	
	public function last_query() {
		return $this->last_query;
	}
	
	public function affected_rows() {
		$t = $this->type;
		return $t->affected_rows();
	}

	public function disconnect() {
		if($this->type !== null) {
			$this->type->disconnect();
			$this->type = null;
		}
	}
}
?>