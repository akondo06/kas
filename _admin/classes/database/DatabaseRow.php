<?php

class KASDatabaseRow {
	protected $db;
	protected $validator;

	protected $table = 'test';
	protected $primaryKey = 'id';

	protected $defaults = array(
		'id' => 0
	);

	protected $model;
	
	public function __construct(array $model = array()) {
		if(class_exists('KAS') && class_exists('KASValidator')) {
			$KAS = KAS::instance();
			$this->db = $KAS->db_connection();
			$this->model($model);
		} else {
			throw new Exception("KAS Class not found!", 1);
		}
	}

	public function model($data = null) {
		if($data === null) {
			return $this->model;
		} else if(is_array($data)) {
			$db = $this->db;
			$model = array();
			foreach($this->defaults as $key => $value) {
				if(array_key_exists($key, $data)) {
					$model[$key] = $db->secure($data[$key]);
				} else {
					$model[$key] = $db->secure($this->defaults[$key]);
				}
			}
			$this->model = $model;
			$this->validator = new KASValidator($this->model);
		}
	}

	public function get($data = array(), $setModel = true) {
		return $this->gen_get($data, null, $setModel);
	}

	public function save() {
		return $this->gen_save();
	}

	public function delete() {
		return $this->gen_delete();
	}

	public function validation() {
		return true;
	}

	public function gen_get($data = array(), $table = null, $setModel = true) {
		if(!(is_array($data) || is_numeric($data))) { return null; }
		$identifier = $this->primaryKey;
		if(is_numeric($data)) { $this->model[$identifier] = $data; $data = array($identifier.'' => $data); }
		if(count($data) === 0) { return null; }
		if($table === null) { $table = $this->table; }
		if(!is_string($table)) { return null; }
		$result = null;

		$fields = $this->_modelFields();
		$where = $this->_where($data);
		
		if($fields != "" && $where != "") {
			$db = $this->db;
			$query = "SELECT ".$fields." FROM `".$table."` WHERE ".$where." LIMIT 1";
			$queryResult = $db->query($query);
			if(is_array($queryResult) && count($queryResult) > 0) {
				$result = (array) $queryResult[0];
				if($setModel === true) {
					$this->model($result);
				}
			}
		}
		return $result;
	}

	public function gen_save($table = null) {
		if($table === null) { $table = $this->table; }
		if(!is_string($table)) { return null; }
		$result = false;
		$isValid = $this->_is_valid();

		if($isValid !== true) { return $isValid; }

		if(is_string($table)) {
			$model = $this->model;
			$identifier = $this->primaryKey;
			$fields = $this->_modelFields();
			$values = $this->_modelValues();
			$fieldsAndValues = $this->_stringify($model, ', ');
			
			if($fields != "" && $values != "" && $fieldsAndValues != "") {
				$where = "";
				$db = $this->db;
				$query = "INSERT INTO ".$table."(".$fields.") VALUES(".$values.")";
				if(array_key_exists($identifier, $model)) {
					$identifierValue = $model[$identifier];
					if(!empty($identifierValue)) {
						if($this->_exists($identifierValue)) {
							$where = $this->_where(array($identifier.'' => $identifierValue));
							if($where != "") {
								$query = "UPDATE ".$table." SET ".$fieldsAndValues." WHERE ".$where." LIMIT 1";
							}
						}
					}
				}
				$queryResult = $db->query($query);
				if($db->affected_rows() > 0) {
					$result = true;
				}
			}
		}
		return $result;
	}

	public function gen_delete($table = null) {
		if($table === null) { $table = $this->table; }
		if(!is_string($table)) { return null; }
		$result = false;
		
		$model = $this->model;
		$identifier = $this->primaryKey;

		if(array_key_exists($identifier, $model)) {
			$identifierValue = $model[$identifier];
			if(!empty($identifierValue)) {
				$where = $this->_where(array($identifier.'' => $identifierValue));
				if($where != "") {
					$db = $this->db;
					$query = "DELETE FROM ".$table." WHERE ".$where." LIMIT 1";

					$queryResult = $db->query($query);
					if($db->affected_rows() > 0) {
						$result = true;
					}
				}
			}
		}
		return $result;
	}

	public function _is_valid() {
		$method = 'validate';
		$result = true;
		if(method_exists($this, $method)) {
			try {
				$this->$method();
			} catch (Validator_Exception $e) {
				$result = $e->getErrors();
			} catch (Exception $e) {
				$result = false;
			}
		}
		return $result;
	}

	protected function _exists($identifierValue, $table = null) {
		return $this->gen_get($identifierValue, $table, false) ? true : false;
	}

	protected function _where(array $data, array $exclude = array()) {
		return $this->_stringify($data, ' AND ', $exclude);
	}

	protected function _modelFields(array $exclude = array()) {
		return $this->_stringify($this->model, ', ', $exclude, true, false);
	}

	protected function _modelValues(array $exclude = array()) {
		return $this->_stringify($this->model, ', ', $exclude, false);
	}

	protected function _stringify(array $data, $delimiter = ', ', $exclude = array(), $includeFields = true, $includeValues = true) {
		$result = "";
		foreach($data as $key => $value) {
			if(!in_array($key, $exclude) && ((is_string($value)) || (is_numeric($value) && $value > 0) || is_bool($value))) {
				if(is_bool($value)) { if($value) { $value = 1; } else { $value = 0; } }
				if(is_string($value)) { $value = "'".$value."'"; }
				if(strlen($result) > 0) { $result .= ", "; }
				
				if($includeFields === false && $includeValues === true) {
					$result .= $value;
				} else if($includeFields === true && $includeValues === false) {
					$result .= $key;
				} else {
					$result .= "`".$key."`=".$value;
				}
			}
		}

		return $result;
	}
}

?>