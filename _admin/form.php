<?php

class Form {
	// Types (if starts with "_" then it's subtype...)
	private $_label = "<label>[LABEL]</label>";
	private $label = "";
	private $_selected = " selected=\"selected\"";
	private $_disabled = " disabled=\"disabled\"";
	private $_values = "<option value=\"[0]\"[_SELECTED]>[1]</option>";
	private $input = "<div class=\"[CLASSES]\">[_LABEL]<input type=\"[TYPE]\" name=\"[NAME]\" placeholder=\"[PLACEHOLDER]\" value=\"[VALUE]\"[_DISABLED] /></div>\n";
	private $textarea = "<div class=\"[CLASSES]\">[_LABEL]<textarea name=\"[NAME]\" cols=\"[COLS]\" rows=\"[ROWS]\">[VALUE]</textarea></div>\n";
	private $select = "<div class=\"[CLASSES]\">[_LABEL]<select name=\"[NAME]\">[+VALUES]</select></div>\n";
	private $button = "<div class=\"[CLASSES]\">[_LABEL]<input type=\"[TYPE]\" name=\"[NAME]\" value=\"[VALUE]\" [_DISABLED] /><div class=\"clear\"></div></div>\n";
	private $clear = "<div class=\"clear\"></div>\n";

	// Default Args
	private $input_defaults = array("classes" => "inputWrapper", "type" => "text", "name" => "", "value" => "", "placeholder" => "");
	private $textarea_defaults = array("classes" => "inputWrapper", "name" => "", "cols" => 30, "rows" => 10);
	private $select_defaults = array("classes" => "inputWrapper", "name" => "", "values" => array("none" => "No Values"), "selected" => "none");
	private $button_defaults = array("classes" => "buttonWrapper", "type" => "submit", "name" => "submit", "value" => "Submit");

	public function __construct() {
		$this->label = $this->_label;
	}

	private function generate($element, array $params) {
		$defaults = $element.'_defaults';
		if(is_array($params)) {
			if(property_exists($this, $defaults)) {
				$params = array_merge($this->$defaults, $params);
			}
		} else {
			if(property_exists($this, $defaults)) {
				$params = $this->$defaults;
			} else {
				return;
			}
		}

		$tokens = $this->tokens($element);

		$what = array();
		$with = array();

		foreach ($tokens as $token) {
			if($token[0] === '_') {
				if(array_key_exists(trim($token, '_ '), $params)) {
					$with[] = $this->generate($token, $params);
				} else {
					$with[] = "";
				}
			} else if($token[0] === '+') {
				$tkn = trim($token, '+ ');
				if(array_key_exists($tkn, $params)) {
					$_with = "";
					foreach ($params[$tkn] as $key => $value) {
						$sub_args = array($key, $value);
						if(array_key_exists('selected', $params) && $key == $params['selected']) {
							$sub_args['selected'] = $params['selected'];
						}
						$_with .= $this->generate("_".$tkn, $sub_args);
					}
					$with[] = $_with;
				} else {
					$with[] = "";
				}
			} else {
				if(array_key_exists($token, $params)) {
					$with[] = $params[$token];
				} else {
					$with[] = "";
				}
			}
			$what[] = "[".strtoupper($token)."]";
		}

		return str_replace($what, $with, $this->$element);
	}

	public function __call($name, $arguments) {
		$properties = array("label", "clear", "input", "textarea", "select", "button");
		$name = strtolower($name);
		if(in_array($name, $properties)) {
			if(!array_key_exists(0, $arguments)) {
				$arguments = array(array());
			}
			if($name === "label" && is_string($arguments[0])) {
				$arguments[0] = array("label" => $arguments[0]);
			}
			if(!is_array($arguments[0])) {
				$arguments[0] = array($arguments[0]);
			}
			echo $this->generate($name, $arguments[0]);
		} else {
			throw new BadMethodCallException('Call to undefined method ' . __CLASS__ . '->' . $name);
		}
	}

	private function tokens($element) {
		$tokens = array();
		preg_match_all("/\[[A-Za-z0-9\_\+]+\]/i", $this->$element, $tokens);
		return array_map(function($element) { return strtolower(trim($element, "[] ")); }, $tokens[0]);
	}
}

?>