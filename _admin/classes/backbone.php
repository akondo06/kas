<?php

define('KASROOT', "../");
require_once(KASROOT."_kas/classes/db_config.php");
require_once(KASROOT."_kas/classes/cache.php");
require_once(KASROOT."_kas/classes/database.php");
require_once(KASROOT."_kas/classes/session.php");
require_once(KASROOT."_kas/classes/router.php");
require_once(KASROOT."_kas/classes/kas.php");
require_once(KASROOT."_kas/classes/list.php");

require_once("exceptions.php");
require_once("validator.php");
require_once("validator.php");
require_once("database/DatabaseRow.php");
require_once("database/AdvertismentRow.php");
require_once("fileManager.php");
require_once("section.php");
require_once("form.php");


class Backbone {
	private $sections_path = "sections/";
	private $section = "dashboard";

	private $sections = array();

	private static $instance = null;
	
	public static function instance() {
		if(self::$instance === null) {
			self::$instance = new Backbone();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_sections();

		// Secure the user input...
		$db = KAS::instance()->db();

		// Check for logout
		$this->check_for_logout();
	}


	public function check_for_user() {
		if($this->user_logged_in() == false) {
			if(array_key_exists('email', $_POST) && array_key_exists('password', $_POST)) {
				$KAS = KAS::instance();

				$user = $KAS->query("SELECT id, email, nickname FROM users WHERE email='".$_POST['email']."' AND password='".md5($_POST['password'])."' LIMIT 1");
				if(is_array($user) && array_key_exists('0', $user)) {
					$KAS->session()->set('user', $user[0]);
					return true;
				} else {
					$this->message('Wrong email or password!', 'error');
				}
			}
		} else {
			return true;
		}
		return false;
	}

	public function user_logged_in() {
		$KAS = KAS::instance();
		if($KAS->session()->get('user') !== null) {
			return true;
		}
		return false;
	}

	public function user_details() {
		if($this->user_logged_in()) {
			$KAS = KAS::instance();
			return $KAS->session()->get('user');
		}
		return null;
	}

	public function check_for_logout() {
		if($this->user_logged_in() && array_key_exists('action', $_GET) && $_GET['action'] == 'logout') {
			$KAS = KAS::instance();
			$KAS->session()->set('user', null);
			$KAS->redirect_to('index.php');
		}
	}

	public function layout($section, $sub=null) {
		if(is_string($section) && array_key_exists($section, $this->sections) && is_a($this->sections[$section], "Section")) {
			if(is_string($sub) && method_exists($this->sections[$section], "get_".$sub)) {
				$template_url = "layout/".$section."-".$sub.".tpl";
			} else {
				$template_url = "layout/".$section.".tpl";
			}

			if(file_exists($template_url)) {
				return $template_url;
			}
		}
		return null;
	}

	public function current_section() {
		if(array_key_exists('section', $_GET) && array_key_exists($_GET['section'], $this->sections)) {
			$this->section = $_GET['section'];
		}
	}

	public function menu() {
		$menu = array();

		foreach($this->sections as $key => $value) {
			if(is_array($value->menu())) {
				$menu[$key] = $value->menu();
			}
		}

		return $menu;
	}

	public function content() {
		$section = $this->section();
		return $section->layout();
	}

	public function section() {
		return $this->sections[$this->section];
	}

	public function url($section=null, $sub=null) {
		$return = "index.php";
		if(is_string($section) && array_key_exists($section, $this->sections) && is_a($this->sections[$section], "Section")) {
			$return = "?section=".$section;
			if(is_string($sub)) {
				$return .= "&sub=".$sub;
			}
		}
		return $return;
	}

	public function site_url() {
		$KAS = KAS::instance();
		return $KAS->url();
	}

	public function current_url() {
		$KAS = KAS::instance();
		return $KAS->current_url();
	}

	public function redirect($section, $sub=null) {
		$url = $this->url($section, $sub);
		if($url != null) {
			$KAS = KAS::instance();
			$KAS->redirect_to($url);
		}
	}

	public function message($msg=null, $type="success") {
		$KAS = KAS::instance();
		if(is_string($msg)) {
			if(!($type == 'success' || $type == 'error' || $type == 'warning' || $type == 'info')) {
				$type = "success";
			}
			$KAS->session()->set('message', array('type' => $type, 'message' => $msg));
		} else {
			$msg = $KAS->session()->get('message');
			$KAS->session()->set('message', null);
			return $msg;
		}
	}

	public function object_has_fields($object, $fields) {
		if(!empty($object) && !is_object($object) && !empty($fields) && !is_object($fields)) { return false; }
		$result = true;
		foreach($fields as $field => $value) {
			$result = property_exists($object, $field) && $object->$field == $value;
			if($result == false) { break; }
		}
		return $result;
	}

	private function load_sections() {
		if($handle = opendir($this->sections_path)) {

		    while(false !== ($entry = readdir($handle))) {
		    	if($entry != "." && $entry != ".." && is_dir($this->sections_path.$entry) && file_exists($this->sections_path.$entry."/data.php")) {
		    		require_once($this->sections_path.$entry."/data.php");
		    		$class_name = ucfirst($entry);
		    		if(class_exists($class_name)) {
			    		$class = new $class_name();
			    		if($class instanceof Section) {
			    			$this->sections[$entry] = $class;
			    		}
			    	}
			    }
		    }

		    closedir($handle);
		}
	}

}

?>