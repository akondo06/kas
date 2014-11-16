<?php

abstract class Section {
	abstract public function menu();
	abstract public function content();

	public function id() {
		return strtolower(get_class($this));
	}

	public function template() {
		if(array_key_exists('sub', $_GET) && method_exists($this, "get_".$_GET['sub'])) {
			$method_name = "get_".$_GET['sub'];
			return $this->$method_name();
		} else {
			return $this->content();
		}
	}

	public function layout() {
		$bone = Backbone::instance();
		$template_uri = $bone->layout($this->id());

		if(array_key_exists('sub', $_GET)) {
			if(method_exists($this, "get_".$_GET['sub'])) {
				$tmpl_layout = $bone->layout($this->id(), $_GET['sub']);
				if($tmpl_layout != null) {
					$template_uri = $tmpl_layout;
				} else {
					$template_uri = null;
				}
			} else {
				$template_uri = null;
			}
		}

		if(is_string($template_uri)) {
			include($template_uri);
			return;
		}

		echo "ERROR: Could not find the layout for this section!";
	}

	public function exists_in_menu($sub) {
		$menu = $this->menu();
		$submenu = $menu['submenu'];
		return array_key_exists($sub, $submenu);
	}

	public function url($sub=null) {
		
		$bone = Backbone::instance();
		if($sub === true && array_key_exists('sub', $_GET)) {
			$sub = $_GET['sub'];
		}
		return $bone->url($this->id(), $sub);
	}

	public function current_url_page() {
		global $_GET;
		if(array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
			return intval($_GET['page']);
		}
		return 1;
	}

}

?>