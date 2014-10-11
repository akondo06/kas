<?php

class Home implements KASSection {
	private $title = null;

	public function __construct() {
		$KAS = KAS::instance();
		
		// Title
		$this->title = $KAS->title("home", array());

		if(isset($_POST['search'])) {
			$KAS->redirect_to($KAS->link("tag", array("[SLUG]" => $_POST['search'], "[PAGE]" => 1)));
		}
	}
	
	public function title() {
		return $this->title;
	}
	
	public function file() {
		return "home.tpl";
	}
	
	public function tags() {
		return null;
	}
	
	public function description() {
		return null;
	}
}

?>