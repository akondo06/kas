<?php

class Tag implements KASSection {
	private $page_nr = 1;
	private $title = null;
	private $file = "listing.tpl";
	private $tags = null;
	private $description = null;
	
	private $name = null;
	private $games_no = 0;

	public function __construct() {
		$KAS = KAS::instance();
		$args = $KAS->router()->args();
		$list = KAS_List::instance();
		
		$data = null;
		
		if(array_key_exists('slug', $args)) {
			$data = $list->get_tag_no($args['slug']);
		} else {
			
		}
		
		if($data == null) {
			$KAS->redirect_to($KAS->link("404"));
		}

		// Page number
		if(isset($args['page']) && is_numeric($args['page'])) {
			$this->page_nr = $args['page'];
		}
		

		// Check if page number is in range!
		if($this->page() < 1 || $this->page() > ceil($data->games_no / $KAS->setting("per_page"))) {
			$KAS->redirect_to($KAS->link("404"));
		}

		// Title
		$this->title = $KAS->title($KAS->router()->current_route(), array("[NAME]" => $args['slug'], "[PAGE]" => $this->page()));
		
		// Tags
		$this->tags = "Tag keywords....";
		
		// Description
		$this->description = "Tag Description....";
		
		// Name
		$this->name = ucfirst($args['slug']);

		// Total Games
		$this->games_no = $data->games_no;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function title() {
		return $this->title;
	}
	
	public function file() {
		return $this->file;
	}
	
	public function tags() {
		return $this->tags;
	}
	
	public function description() {
		return $this->description;
	}

	public function page() {
		return $this->page_nr;
	}

	public function games_no() {
		return $this->games_no;
	}
}

?>