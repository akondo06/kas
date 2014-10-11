<?php

class Category implements KASSection {
	private $page_nr = 1;
	private $title = null;
	private $file = "listing.tpl";
	private $tags = null;
	private $description = null;
	
	private $id = null;
	private $name = null;
	private $games_no = 0;

	public function __construct() {
		$KAS = KAS::instance();
		$args = $KAS->router()->args();
		$list = KAS_List::instance();
		
		$category = null;
		
		if(array_key_exists('id', $args) && array_key_exists('slug', $args)) {
			$cat_by_id = $list->get_category($args['id']);
			$cat_by_slug = $list->get_category($args['slug']);

			if($cat_by_id->id == $cat_by_slug->id) {
				$category = $cat_by_id;
			}
		} else if(array_key_exists('id', $args)) {
			$category = $list->get_category($args['id']);
		} else if(array_key_exists('slug', $args)) {
			$category = $list->get_category($args['slug']);
		} else {
			
		}
		
		if($category == null) {
			$KAS->redirect_to($KAS->link("404"));
		}

		// Page number
		if(isset($args['page']) && is_numeric($args['page'])) {
			$this->page_nr = $args['page'];
		}

		// Check if page number is in range!
		if($this->page() < 1 || $this->page() > ceil($category->games_no / $KAS->setting("per_page"))) {
			$KAS->redirect_to($KAS->link("404"));
		}

		// Title
		$this->title = $KAS->title($KAS->router()->current_route(), array("[NAME]" => $category->name, "[PAGE]" => $this->page()));
		
		// Load the other details from database
		$list->load_category($category->id);
		
		// Tags
		$this->tags = $category->tags;
		
		// Description
		$this->description = $category->description;
		
		// Id
		$this->id = $category->id;
		
		// Name
		$this->name = $category->name;

		// Total Games
		$this->games_no = $category->games_no;
	}
	
	public function id() {
		return $this->id;
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