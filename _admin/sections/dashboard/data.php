<?php
	
class Dashboard extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Dashboard", 
						"submenu" => array(
										"reset_cache" => "Reset Cache"
									)
						);
		return $return;
	}

	public function content() {
		
	}

	public function get_reset_cache() {
		$bone = Backbone::instance();
		$bone->message('Cache reset successful.');
		$bone->redirect('dashboard');
	}

	private function xml_to_array( $xmlObject, $out = array()) {
		foreach((array) $xmlObject as $index => $node) {
			$out[$index] = (is_object($node)) ? $this->xml_to_array($node) : $node;
		}
		return $out;
	}
	
	private function get_news($url) {
		$info = @simplexml_load_file($url,'SimpleXMLElement', LIBXML_NOCDATA);
		if($info) {
			return $this->xml_to_array($info);
		} else {
			return false;
		}
	}
	
	public function render_news($pattern="<div class=\"announcement\"><div class=\"anntitle\"><a href=\"[LINK]\" target=\"_blank\">[TITLE]</a></div>[DESCRIPTION]</div>\n") {
		$return = "";
		$toreplace = array("[LINK]","[TITLE]","[DESCRIPTION]");
		$xmlinfo = $this->get_news("http://www.kiddoarcadescript.com/news.xml");
		if($xmlinfo) {
			$info = $xmlinfo['item'];
			foreach($info as $item) {
				$replacewith = array($item->link,$item->title,$item->description);
				$return .= str_replace($toreplace,$replacewith,$pattern);
			}
		} else {
			$return = "Error connecting to the server.";
		}
		return $return;
	}
}

?>