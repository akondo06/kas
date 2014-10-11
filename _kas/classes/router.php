<?php

interface KASSection {
	public function title();
	public function file();
	public function tags();
	public function description();
}

class Router {
	private $routes = array();
	private $current_route = "404";
	private $args = null;
	private $section;
	
	public function process() {
		$this->route($this->current_url());
	}
	
	public function current_route() {
		return $this->current_route;
	}

	public function has_route($data) {
		return array_key_exists($data, $this->routes);
	}
	
	public function add_route($name, $regex, $class_file = null) {
		if(is_string($name) && is_string($regex)) {
			$this->routes[$name] = array($regex, $class_file);
		}
	}
	
	private function route($url) {
		// check each route if it matches the current url and set the current route to the corresponding one.
		if($url == "" || empty($url)) {
			$this->current_route = "home";
		} else {
			foreach($this->routes as $to => $from) {
				if(preg_match("/^".$from[0]."$/i", $url, $this->args)) {
					$this->current_route = $to;
					break;
				} else {
				}
			}
		}
	}

	public function section() {
		if($this->section == null) {
			$custom_class_route = $this->routes[$this->current_route][1];
			if($custom_class_route == null) {
				$class_file = __DIR__."/sections/".$this->current_route.".php";
			} else {
				$class_file = $custom_class_route;
			}
			$class_name = $this->file_get_php_classes($class_file);
			if($class_name == null) {
				$class_file = __DIR__."/sections/404.php";
			}
			$class_name = $this->file_get_php_classes($class_file);
			$class_name = $class_name[0];

			if($class_name != null) {
				require_once($class_file);
				$section = new $class_name();
			} else {
				$section = null;
			}
			
			if($section instanceof KASSection) {
				$this->section = $section;
			} else {
				exit("<b>Fatal Error:</b> Could not load the section \"".$this->current_route."\" file.");
			}
		}
		return $this->section;
	}
	
	private function file_get_php_classes($filepath) {
		if(file_exists($filepath)) {
			$php_code = file_get_contents($filepath);
			$classes = $this->get_php_classes($php_code);
			return $classes;
		}
		return null;
	}
	
	private function get_php_classes($php_code) {
		$classes = array();
		$tokens = token_get_all($php_code);
		$count = count($tokens);
		
		for($i=2; $i < $count; $i++) {
			if($tokens[$i-2][0] == T_CLASS && $tokens[$i-1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
				$class_name = $tokens[$i][1];
				$classes[] = $class_name;
			}
		}
		return $classes;
	}
	
	public function current_url() {
		global $_GET;
		if(array_key_exists("query", $_GET)) {
			return $_GET['query'];
		} else {
			return "";
		}
	}
	
	public function args() {
		return $this->args;
	}
}

?>