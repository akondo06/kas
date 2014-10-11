<?php

class Error404 implements KASSection {
	public function __construct() {
		
	}
	
	public function title() {
		return "Page Not Found";
	}
	
	public function file() {
		return "404.tpl";
	}
	
	public function tags() {
		return null;
	}
	
	public function description() {
		return null;
	}
}

?>