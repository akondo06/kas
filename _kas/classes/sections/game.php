<?php

class Game implements KASSection {
	private $title = null;
	private $file = "game.tpl";
	private $tags = null;
	private $description = null;
	private $instructions = null;
	private $walkthrough = null;
	private $id = 0;
	private $name = null;
	private $category = null;
	private $type = 0;
	private $slug = null;
	private $game_file = null;
	private $thumb = null;
	
	private $size = array();


	public function __construct() {
		$KAS = KAS::instance();
		$args = $KAS->router()->args();
		$list = KAS_list::instance();
		
		// Get the game data
		$get = null;
		
		if(array_key_exists('id', $args) && array_key_exists('slug', $args)) {
			$by_id = $list->load_game($args['id']);

			if($by_id->slug == $args['slug']) {
				$get = $by_id;
			}
		} else if(array_key_exists('id', $args)) {
			$get = $list->load_game($args['id']);
		} else if(array_key_exists('slug', $args)) {
			$get = $list->load_game($args['slug']);
		} else {
			
		}
		
		if($get == null) {
			$KAS->redirect_to($KAS->link("404"));
			//exit("<b>Fatal Error</b>: Could not load game from database.");
		}
		
		// Title
		$this->title = $KAS->title($KAS->router()->current_route(), array("[NAME]" => $get->name));
		
		// Tags
		$this->tags = $get->tags;
		
		// Description
		$this->description = $get->description;
		
		// Instructions
		$this->instructions = $get->instructions;

		// Walkthrough
		$this->walkthrough = $get->walkthrough;
		
		// Category
		$this->id = $get->id;
		
		// Name
		$this->name = $get->name;
		
		// Category
		$this->category = $get->category;
		
		// Type
		$this->type = $get->type;
		
		// Slug
		$this->slug = $get->slug;
		
		// File
		$this->game_file = $get->file;
		
		// Thumb
		$this->thumb = $get->thumb;
		
		// Size
		$this->size = array($get->width, $get->height);
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
	
	public function instructions() {
		return $this->instructions;
	}
	
	public function walkthrough() {
		return $this->walkthrough;
	}
	
	public function id() {
		return $this->id;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function category() {
		return $this->category;
	}
	
	public function type() {
		$return = null;
		switch($this->type) {
			case 1: $return = "swf"; break;
			case 2: $return = "unity3d"; break;
			case 3: $return = "html5"; break;
			default: $return = "swf"; break;
		}
		
		return $return;
	}
	
	public function slug() {
		return $this->slug;
	}
	
	public function thumb() {
		if(empty($this->thumb)) {
			$KAS = KAS::instance();
			return $KAS->thumb_url($this->slug());
		}
		return $this->thumb;
	}
	
	public function size() {
		return $this->size;
	}
	
	public function file_url() {
		if(empty($this->game_file)) {
			$KAS = KAS::instance();
			return $KAS->file_url($this->slug(), $this->type());
		}
		return $this->game_file;
	}

}

?>