<?php
	
class Settings extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Settings", 
						"submenu" => array()
						);
		return $return;
	}

	public function content() {
		
	}
}

?>