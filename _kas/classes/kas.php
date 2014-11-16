<?php

class KAS {
	private $settings = null;
	private $db = null;
	private $session = null;
	private $router = null;
	
	private $_themes_folder = "_kas/themes/";
	
	private $_game_thumbs_file_type = "jpg";
	private $_game_thumbs_folder = "_kas/files/img/";
	private $_game_files_folder = "_kas/files/files/";
	
	private static $instance = null;
	
	public static function instance() {
		if(self::$instance === null) {
			self::$instance = new KAS();
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->initiate_session();
		$this->initiate_db();
		$this->initiate_settings();
		$this->initiate_router();
	}
	
	public function name() {
		return "Kiddo Arcade Script";
	}
	
	public function version() {
		return "0.1";
	}
	
	public function db_connection() {
		return $this->db;
	}
	
	public function db() {
		return $this->db_connection();
	}
	
	public function session() {
		return $this->session;
	}
	
	public function router() {
		return $this->router;
	}
	
	public function section() {
		return $this->router()->section();
	}

	public function url() {
		return $this->setting("url");
	}
	
	public function template_path() {
		$setting = $this->setting("theme");
		if($setting != null) {
			return $this->_themes_folder.$setting."/";
		}
		return null;
	}
	
	public function thumbs_path() {
		return $this->_game_thumbs_folder;
	}
	
	public function files_path() {
		return $this->_game_files_folder;
	}
	
	public function template_url() {
		return $this->url().$this->template_path();
	}
	
	public function thumbs_file_type() {
		return $this->_game_thumbs_file_type;
	}

	public function template_layout() {
		return "layout.tpl";
	}

	public function settings() {
		return $this->settings;
	}
	
	public function setting($name) {
		if(isset($this->settings->$name)) {
			return $this->settings->$name;
		}
		return null;
	}
	
	public function setting_pattern($setting, array &$replace) {
		$setting_value = $this->setting($setting);
		if(is_string($setting) && $setting_value != null) {
			return str_replace(array_keys($replace), array_values($replace), $setting_value);
		}
		return null;
	}
	
	public function query($query) {
		return $this->db()->query($query);
	}

	public function title($type, array $replace) {
		if($replace == null) {
			$replace = array();
		}
		$replace['[SITENAME]'] = $this->setting("sitename");
		return $this->setting_pattern($type."_title", $replace);
	}
	
	public function link($type, array $replace = null) {
		if($type == "404") {
			$result = "404";
		} else {
			$result = $this->setting_pattern($type."_route", $replace);
		}
		if($result != null) {
			//return $this->url()."?query=".$result;
			return $this->url().$result;
		}
		return null;
	}
	
	public function thumb_url($data) {
		if(empty($data) || !is_string($data)) {
			return null;
		}
		return $this->url().$this->thumbs_path().$data.".".$this->thumbs_file_type();
	}
	
	public function file_url($data, $type = "swf") {
		if(empty($data) || !is_string($data) || empty($type) || !is_string($type)) {
			return null;
		}
		return $this->url().$this->files_path().$data.".".$type;
	}
	
	public function redirect_to($location=null) {
		if($location != null) {
			header("Location: {$location}");
			exit();
		}
	}
	
	public function current_url() {
		$return = "http://";
		if($_SERVER["SERVER_PORT"] != "80") {
			$return .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$return .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $return;
	}
	
	public function ends_with($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if($testlen > $strlen) {
			return false;
		}
		return substr_compare($string, $test, -$testlen) === 0;
	}

	public function limit_string($string, $limit) {
		if(strlen($string) > $limit) { return substr($string, 0, $limit-3)."..."; } else { return $string; }
	}
	
	private function initiate_session() {
		$this->session = new Session();
		$this->session()->register();
	}
	
	private function initiate_db() {
		if(class_exists("Database")) {
			// Default values.
			$type = "mysql";
			$cache_folder = "_kas/cache/";
			$cache_time = 0; // default 3600
			
			// Check for faster and better ones.
			if(function_exists("mysqli_connect")) {
				$type = "mysqli";
			}
			if($this->setting("cache") != null) {
				$cache_folder = $this->setting("cache");
			}
			$chtm = $this->setting("cache_time");
			if($chtm != null && is_numeric($chtm) && $chtm > -1) {
				$cache_time = $chtm;
			}
			
			// Create the database object and connect to the database.
			$this->db = new Database($type, $cache_folder);
			$this->db->connect(DB_SERVER_ADDRESS, DB_SERVER_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
			$this->db->cache_time($cache_time);

			// Secure the input data!
			if(isset($_GET)) {
				$_GET = $this->db->secure($_GET);
			}
			if(isset($_POST)) {
				$_POST = $this->db->secure($_POST);
			}
		} else {
			exit("Fatal Error: Required Class \"Database\" not found!");
		}
	}
	
	private function initiate_settings() {
		$db = $this->db_connection();
		$result = $db->query("SELECT * FROM settings WHERE id=1 LIMIT 1;");
		if(is_array($result) && (count($result) > 0) && is_object($result[0])) {
			$obj = $result[0];
			unset($obj->id);
			$this->settings = $obj;
		} else {
			exit("Error: Could not get the settings from the Database.");
		}
	}

	private function initiate_router() {
		global $_GET, $_POST;
		$this->router = new Router();
		$r = $this->router();
		
		// Define the replace here...
		$replace = array("[NAME]", "[SLUG]", "[ID]", "[PAGE]", "/");
		$with = array("(?P<name>[A-Za-z0-9\-\_]+)", "(?P<slug>[A-Za-z0-9\-\_]+)", "(?P<id>[0-9]+)", "(?P<page>[0-9]+)", "\\/");
		
		// Get the settings, apply the changes and store the routes.
		$settings = $this->settings();
		
		foreach($settings as $index => $value) {
			// if the setting ends with _route it means that is a route and it should be added to the map.
			if($this->ends_with($index, "_route")) {
				$ind = str_replace("_route", "", $index);
				$regex = "".str_replace($replace, $with, $value)."";
				$r->add_route($ind, $regex);
			}
		}
		
		// Add the required routes
		$r->add_route("404", "");
		$r->add_route("home", "");
		//$r->add_route("akondo", "akondo\/(?P<id>[0-9]+)", "akondo.php"); // Custom Route are declared like this!
		
		$r->process();
	}
}

?>