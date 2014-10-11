<?php

class KAS_List {
	private $categories_raw = null;
	
	private $orderby_list_category = array("id", "name");
	private $orderby_list_games = array("id", "name", "plays", "newest", "random", "rating", "playing");
	private $order_list = array("desc", "asc");
	
	private static $instance = null;
	
	public static function instance() {
		if(self::$instance === null) {
			self::$instance = new KAS_List();
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->load_categories();
	}
	
	public function categories(array $args = null) {
		$return = "";
		$default_args = array(
			"limit" => array("start" => 0, "end" => 10),
			"order_by" => "name",
			"order" => "ASC",
			"parent" => 0,
			"pattern" => "[BETWEEN]<li><a href=\"[LINK]\">[NAME]</a>[SUBCATEGORIES]</li>\n",
			"before_subcategories" => "<ul>\n",
			"after_subcategories" => "</ul>\n"
		);
		if(is_array($args)) {
			$args = array_merge($default_args, $args);
		}
		if($args == null) {
			$args = &$default_args;
		}
		
		$KAS = KAS::instance();
		$db = $KAS->db();
		
		// Check 'limit'
		$v_limit = $this->verify_limit($args['limit']);
		$args['limit'] = ($v_limit) ? $v_limit : $default_args['limit'];
		
		// Check 'order by'
		$v_orderby = in_array($args['order_by'], $this->orderby_list_category);
		$args['order_by'] = ($v_orderby) ? $args['order_by'] : $default_args['order_by'];
		
		// Check 'order'
		$v_order = in_array(strtolower($args['order']), $this->order_list);
		$args['order'] = ($v_order) ? strtoupper($args['order']) : $default_args['order'];
		
		// Check 'parent'
		$v_parent = is_numeric($args['parent']) && $args['parent'] >= 0;
		$args['parent'] = ($v_parent) ? $args['parent'] : $default_args['parent'];
		
		// Check 'pattern'
		$v_pattern = is_string($args['pattern']);
		$args['pattern'] = ($v_pattern) ? $args['pattern'] : $default_args['pattern'];
		
		//$result = $db->query("SELECT id, slug, name FROM `categories` WHERE `parent`=".$args['parent']." ORDER BY ".$args['order_by']." ".$args['order']." LIMIT ".$args['limit']['start'].",".$args['limit']['end'].";");
		
		$result = $this->get_categories($args['parent']);

		if($result != null && is_array($result) && count($result) > 0) {
			$to_replace_link = array("[ID]", "[SLUG]", "[NAME]");
			$to_replace = array("[ID]", "[SLUG]", "[NAME]", "[LINK]", "[BETWEEN]", "[SUBCATEGORIES]");
			
			if($args['order_by'] == "name") { usort($result, function($a, $b) { return strcmp($a->name, $b->name); }); }
			if($args['order'] == "DESC") { $result = array_reverse($result); }
			if(count($result) > $args['limit']['end']-$args['limit']['start']) { $result = array_slice($result, $args['limit']['start'], $args['limit']['end']); }
			
			foreach($result as $category) {
				$replace_with_link = array_combine($to_replace_link, array($category->id, $category->slug, $category->name));
				
				$subcategories = null;
				if(substr_count($args['pattern'], "[SUBCATEGORIES]") > 0) {
					$subcategories = $this->categories(array("limit" => 20, "parent" => $category->id));
					if($subcategories != null) {
						$subcategories = $args['before_subcategories'].$subcategories.$args['after_subcategories'];
					}
				}
				
				$replace_with = array($category->id, $category->slug, $category->name, $KAS->link("category", $replace_with_link), "", $subcategories);
				$return .= str_replace($to_replace, $replace_with, $args['pattern']);
			}
		} else {
			$return = null;
		}
		
		return $return;
	}

	public function games(array $args = null) {
		$return = "";
		$default_args = array(
			"limit" => array("start" => 0, "end" => 10),
			"page" => 0,
			"order_by" => "name",
			"order" => "ASC",
			"category" => null,
			"exclude" => null,
			"featured" => false,
			"search" => null,
			"details" => "default",
			"pattern" => "[BETWEEN]<li><a href=\"[LINK]\" title=\"Play [NAME]\"><span class=\"thumb\"><img src=\"[THUMB]\" alt=\"\" /></span><span class=\"title\">[NAME]</span></a></li>\n",
		);
		if(is_array($args)) {
			$args = array_merge($default_args, $args);
		}
		if($args == null) {
			$args = &$default_args;
		}
		
		$KAS = KAS::instance();
		$db = $KAS->db();
		
		// Check 'limit'
		$v_limit = $this->verify_limit($args['limit']);
		$args['limit'] = ($v_limit) ? $v_limit : $default_args['limit'];

		// Check 'page'
		$v_page = is_numeric($args['page']) && intval($args['page']) > 0;
		$args['page'] = ($v_page) ? intval($args['page']) : $default_args['page'];
		if($args['page'] > 0) {
			$args['limit']['start'] = ($args['page'] - 1) * $args['limit']['end'];
		}

		// Check 'order by'
		$v_orderby = $this->verify_game_orderby($args['order_by']);
		$args['order_by'] = ($v_orderby != null) ? $v_orderby : $default_args['order_by'];
		
		// Check 'order'
		$v_order = in_array(strtolower($args['order']), $this->order_list);
		$args['order'] = ($v_order) ? strtoupper($args['order']) : $default_args['order'];
		
		// Check 'category'
		$v_category = $this->verify_one_or_more($args['category']);
		if($v_category != null) {
			$args['category'] = $v_category;
			
			$s_category = " AND (";
			$x = 0;
			foreach($args['category'] as $item) {
				if($x++ > 0) {
					$s_category .= " OR ";
				}
				$s_category .= "category = ".$item;
			}
			$s_category .= ")";
		} else {
			$args['category'] = $default_args['category'];
			$s_category = "";
		}
		
		// Check 'exclude'
		$v_exclude = $this->verify_one_or_more($args['exclude']);
		if($v_exclude != null) {
			$args['exclude'] = $v_exclude;
			
			$s_exclude = " AND (";
			$x = 0;
			foreach($args['exclude'] as $item) {
				if($x++ > 0) {
					$s_exclude .= " AND ";
				}
				$s_exclude .= "id != ".$item;
			}
			$s_exclude .= ")";
		} else {
			$args['exclude'] = $default_args['exclude'];
			$s_exclude = "";
		}
		
		// Check 'featured'
		$v_featured = is_bool($args['featured']) && $args['featured'] == true;
		$s_featured = ($v_featured) ? " AND status = 2" : "";

		// Check 'search'
		$v_search = $args['search'] != null && !empty($args['search']);
		$s_search = ($v_search) ? " AND ((name LIKE '%".$db->secure($args['search'])."%') OR (description LIKE '%".$db->secure($args['search'])."%'))" : "";
		
		// Check 'details'
		$v_details = array('default' => 'id, slug, name, thumb, description, category, plays', 'all' => '*');
		$s_details = (array_key_exists($args['details'], $v_details)) ? $v_details[$args['details']] : $v_details[$default_args['details']];

		// Check 'pattern'
		$v_pattern = is_string($args['pattern']);
		$args['pattern'] = ($v_pattern) ? $args['pattern'] : $default_args['pattern'];
		
		// Run the query
		$query = "SELECT ".$s_details." FROM games WHERE status > 0".$s_category."".$s_exclude."".$s_featured."".$s_search." ORDER BY ".$args['order_by']." ".$args['order']." LIMIT ".$args['limit']['start'].",".$args['limit']['end'].";";
		$result = $db->query($query);
		
		if($result != null && is_array($result) && count($result) > 0) {
			$to_replace_link = array("[ID]", "[SLUG]", "[NAME]");
			$to_replace = array("[ID]", "[SLUG]", "[NAME]", "[LINK]", "[BETWEEN]", "[THUMB]", "[DESCRIPTION]", "[SHORTDESC]", "[CATEGORYNAME]", "[PLAYS]");
			
			foreach($result as $game) {
				$replace_with_link = array_combine($to_replace_link, array($game->id, $game->slug, $game->name));
				
				if(!isset($game->thumb) || empty($game->thumb)) {
					$game->thumb_url = $KAS->thumb_url($game->slug);
					$thumb = $game->thumb_url;
				} else {
					$thumb = $game->thumb;
				}

				$short_description = $KAS->limit_string($game->description, 126);
				$category = $this->get_category($game->category);
				
				$replace_with = array($game->id, $game->slug, $game->name, $KAS->link("game", $replace_with_link), "", $thumb, $game->description, $short_description, $category->name, $game->plays);
				$return .= str_replace($to_replace, $replace_with, $args['pattern']);
			}
		} else {
			$return = null;
		}
		
		return $return;
	}

	public function pagination(array $args = null) {
		$return = "";
		$default_args = array(
			"display" => 6, // just use odd number for now ...
			"current" => "section",
			"per_page" => "kas",
			"total" => "section",
			"section" => null,
			"base_link" => "auto",
			"pattern" => "[BETWEEN]<li><a href=\"[LINK]\">[NR]</a></li>\n",
			"pattern_current" => "[BETWEEN]<li class=\"selected\"><a href=\"[LINK]\">[NR]</a></li>\n"
		);
		if(is_array($args)) {
			$args = array_merge($default_args, $args);
		}
		if($args == null) {
			$args = &$default_args;
		}
		
		$KAS = KAS::instance();
		$router = $KAS->router();

		// Check Display
		if(!(is_numeric($args['display']) || $args['display'] > 2)) {
			$args['display'] = $default_args['display'];
		}

		// Check Current
		if(!(is_numeric($args['current']) || $args['current'] > 2)) {
			$args['current'] = $default_args['current'];
		}
		if(is_string($args['current']) && $args['current'] == "section") {
			$args['current'] = $KAS->section()->page();
		}

		// Check Per Page
		if(!(is_numeric($args['per_page']) || $args['per_page'] > 0)) {
			$args['per_page'] = $default_args['per_page'];
		}
		if(is_string($args['per_page']) && $args['per_page'] == "kas") {
			$args['per_page'] = $KAS->setting("per_page");
		}

		// Check Total
		if(!(is_numeric($args['total']) || $args['total'] > 0)) {
			$args['total'] = $default_args['total'];
		}
		if(is_string($args['total']) && $args['total'] == "section") {
			$args['total'] = $KAS->section()->games_no();
		}
		$args['total'] = ceil($args['total'] / $args['per_page']);

		// Check Section
		if($args['section'] == null || !($router->has_route($args['section']))) {
			$args['section'] = $router->current_route();
		}

		// Check Base link
		if($args['base_link'] == null || (substr_count($args['base_link'], "[PAGE]") == 0)) {
			$args['base_link'] = $default_args['base_link'];
		}

		// Check Pattern
		if($args['pattern'] == null || !is_string($args['pattern'])) {
			$args['pattern'] = $default_args['pattern'];
		}

		// Check Pattern current
		if($args['pattern_current'] == null || !is_string($args['pattern_current'])) {
			$args['pattern_current'] = $default_args['pattern'];
		}

		if($args['total'] > 1) {
			if($args['display'] > $args['total']) {
				$start_at = 1;
				$end_at = $args['total'];
			} else {
				$half = (int) ($args['display'] / 2);
				$start_at = $args['current'] - $half;
				$end_at = $args['current'] + $half-1;
				
				if($start_at <= 0) {
					$end_at += abs($start_at)+1;
					$start_at = 1;
				}
				if($end_at > $args['total']) {
					$start_at -= $end_at - $args['total'];
					$end_at = $args['total'];
				}
			}
			
			$to_replace = array("[NR]", "[LINK]", "[BETWEEN]");

			for($i = $start_at; $i <= $end_at; $i++) {
				$link = $this->pagination_link($i, $args['total'] * $args['per_page'], $args['base_link'], $args['current']);
				$replace_with = array($i, $link, "");

				$pattern = $args['pattern'];
				if($i == $args['current']) {
					$pattern = $args['pattern_current'];
				}
				$return .= str_replace($to_replace, $replace_with, $pattern);
			}
		}
		return $return;
	}

	public function pagination_link($number, $total=0, $base_link="auto", $current_page_nr = "section", $per_page = "kas") {
		$KAS = KAS::instance();
		if($current_page_nr == "section") {
			$current_page_nr = $KAS->section()->page();
		}
		if($per_page == "kas") {
			$per_page = $KAS->setting("per_page");
		}

		if(is_numeric($total) && $total > 0) {
			$total = intval($total);
		} else {
			$total = 1;
		}

		if(is_numeric($number) && $number > 0) {
			$number = intval($number);
		} else if($number == "prev") {
			$number = $current_page_nr-1;
		} else if($number == "current") {
			$number = $current_page_nr;
		} else if($number == "next") {
			$number = $current_page_nr+1;
		} else {
			$number = 0;
		}
		$total_pages = ceil($total / $per_page);

		if($number > 0 && $number <= $total_pages) {
			if($base_link == null || (substr_count($base_link, "[PAGE]") == 0)) {
				$base_link = "auto";
			}

			if($base_link == "auto") {
				$router = $KAS->router();
				$site_args = $router->args();
				$base_link_replace_with = array();
				foreach($site_args as $arg => $val) {
					if(strtolower($arg) != "page" && !is_numeric($arg)) {
						$base_link_replace_with['['.strtoupper($arg).']'] = $val;
					}
				}

				$section = $router->current_route();
				if(substr_count($section, "_page") == 0) {
					$section .= "_page";
				}

				$base_link_replace_with['[PAGE]'] = $number;
				$link = $KAS->link($section, $base_link_replace_with);
			} else {
				$link = str_replace("[PAGE]", $number, $base_link);
			}

			return $link;
		}
	}
	
	
	// Getters
	public function get_category($data = null, $parentTree = 1) {
		$categories = self::load_categories();
		
		if(is_numeric($data)) {
			foreach($categories as $category) {
				if($category->id == $data) {
					if(is_numeric($category->parent) && $category->parent != 0 && $parentTree == 1) {
						$category->parent = $this->get_category($category->parent);
					}
					return $category;
				}
			}
		} else if(is_string($data)) {
			foreach($categories as $category) {
				if($category->slug == $data) {
					if(is_numeric($category->parent) && $category->parent != 0 && $parentTree == 1) {
						$category->parent = $this->get_category($category->parent);
					}
					return $category;
				}
			}
		} else if(is_array($data)) {
			$return = array();
			foreach($categories as $category) {
				if(in_array($category->id, $data) || in_array($category->slug, $data)) {
					if(is_numeric($category->parent) && $category->parent != 0 && $parentTree == 1) {
						$category->parent = $this->get_category($category->parent);
					}
					$return[] = $category;
				}
			}
			return $return;
		}
		
		return null;
	}
	
	public function get_categories($parent = null) {
		$categories = self::load_categories();
		if(is_numeric($parent) && $parent >= 0) {
			$return = array();
			foreach($categories as $category) {
				$current_parent = $category->parent;
				if((is_numeric($current_parent) && $current_parent == $parent) || (is_object($current_parent) && property_exists($current_parent, 'id') && $current_parent->id == $parent)) {
					$return[] = $category;
				}
			}
			return $return;
		}
		
		return $categories;
	}

	public function get_tag_no($data = null) {
		if(is_string($data) && !empty($data)) {
			$KAS = KAS::instance();
			$db = $KAS->db();
			$result = $db->query("SELECT COUNT(*) AS games_no FROM games WHERE status > 0 AND ((name LIKE '%".$db->secure($data)."%') OR (description LIKE '%".$db->secure($data)."%'));");
			$return = $result[0];
			return $return;
		}
		
		return null;
	}

	public function get_games_no() {
		$categories = $this->get_categories();
		$result = 0;
		foreach ($categories as $category) {
			$result += $category->games_no;
		}
		return $result;
	}
	
	// Loaders
	private function load_categories() {
		if($this->categories_raw == null) {
			$db = KAS::instance()->db();
			$this->categories_raw = $db->query("SELECT id, slug, name, parent, (SELECT COUNT(*) FROM games WHERE status > 0 && category = categories.id) AS games_no FROM categories ORDER BY id ASC;");
		}
		return $this->categories_raw;
	}
	
	public function load_category($data) {
		$db = KAS::instance()->db();
		
		$data = $db->secure($data);
		if(is_numeric($data)) {
			$what = "`id`=".$data."";
		} else if(is_string($data)) {
			$what = "`slug`='".$data."'";
		} else {
			return null;
		}
		
		$category = $db->query("SELECT id, slug, name, parent, description, tags, (SELECT COUNT(*) FROM games WHERE status > 0 && category = categories.id) AS games_no FROM categories WHERE ".$what." LIMIT 1;");
		$category = $category[0];
		
		$get = $this->get_category($category->id);
		
		foreach($category as $var => $value) {
			if(!isset($get->$var)) {
				$get->$var = $value;
			}
		}
		
		return $get;
	}
	
	public function load_game($data) {
		$KAS = KAS::instance();
		$db = $KAS->db();
		
		$data = $db->secure($data);
		if(is_numeric($data)) {
			$what = "id=".$data."";
		} else if(is_string($data)) {
			$what = "slug='".$data."'";
		} else {
			return null;
		}
		
		$get = $db->query("SELECT * FROM games WHERE ".$what." LIMIT 1;");
		if(is_array($get) && isset($get[0])) {
			$get = $get[0];
			if(!class_exists('Backbone')) {
				$db->query("UPDATE games SET plays = plays+1 WHERE ".$what." LIMIT 1", false);
			}
			if(!isset($get->thumb) || empty($get->thumb)) {
				$get->thumb = $KAS->thumb_url($get->slug);

			}
			if(!isset($get->file) || empty($get->file)) {
				$get->file = $KAS->file_url($get->slug);
			}
			$get->category = $this->get_category($get->category);
			return $get;
		}
		return null;
	}
	
	// Verify
	private function verify_limit($limit) {
		$return = false;
		if(is_numeric($limit)) {
			$return = array("start" => 0, "end" => $limit);
		} else {
			if(is_string($limit)) {
				$limit2 = explode(",", $limit); 
				if((count($limit2) == 2) && (is_numeric($limit2[0]) && is_numeric($limit2[1]))) {
					$return = array("start" => $limit2[0], "end" => $limit2[1]);
				}
			} else if(is_array($limit) && count($limit) == 2 && (is_numeric($limit[0]) && is_numeric($limit[1]))) {
				$return = array("start" => $limit[0], "end" => $limit[1]);
			}
		}
		return $return;
	}
	
	private function verify_game_orderby($data) {
		$return = null;

		if(is_string($data) && in_array($data, $this->orderby_list_games)) {
			if($data == "newest") {
				$return = "added_on";
			} else if($data == "random") {
				$return = "rand()";
			} else if($data == "rating") {
				$return = "votes_up";
			} else if($data == "playing") {
				$return = "last_play";
			} else {
				$return = $data;
			}
		}
		return $return;
	}
	
	private function verify_one_or_more($data) {
		$return = null;
		if(is_numeric($data)) {
			$return = array($data);
		} else {
			if(is_string($data) && substr_count($data, ",") > 0) {
				$return = trim(", ", $data);
				$return = explode(",", $data);
				$return = array_map('trim', $return);
				$return = $this->verify_one_or_more($return);
			} else if(is_array($data)) {
				$all_good = true;
				foreach($data as $number) {
					if(!is_numeric($number)) {
						$all_good = false;
						break;
					}
				}
				
				if($all_good) {
					$return = $data;
				}
			}
		}
		return $return;
	}
	
}

?>