<?php
	
class Games extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Games", 
						"submenu" => array(
										"manage_categories" => "Manage Categories", 
										"add" => "Add Game",
										"gameget" => "GameGET"
									)
						);
		return $return;
	}

	public function content() {

	}

	public function get_edit() {
		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
			$bone = Backbone::instance();
			$KAS = KAS::instance();
			$db = $KAS->db();

			$id = intval($_GET['id']);

			if(array_key_exists('submit', $_POST)) {

				$input_data = (!empty($_FILES)) ? array_merge($_POST, $_FILES) : $_POST;

				// Check if the given data is valid in a way ...
				$data = $this->verify_input($input_data, array("instructions", "walkthrough", "file", "thumb"));

				if($data !== null) {
					$local = $this->game($data->slug);
					if($local === null || $local->id == $id) {
						$db->query("UPDATE games SET name='".$data->name."', slug='".$data->slug."', description='".$data->description."', instructions='".$data->instructions."', category='".$data->category."', tags='".$data->tags."', walkthrough='".$data->walkthrough."', width='".$data->width."', height='".$data->height."', file='', thumb='', status='".$data->status."' WHERE id=".$id." LIMIT 1");

						if($db->affected_rows()) {
							$bone->message('Game "'.$data->name.'" modified successfully!');
							$bone->redirect($this->id());
						} else {
							$bone->message('A problem occured while trying to modify the game!', 'error');
						}
					} else {
						$bone->message('A game with the given slug "'.$data->slug.'" already exists!', 'error');
					}
				} else {
					$bone->message('A field was invalid and the game could not be updated!', 'error');
				}
			}

		} else {
			$bone->redirect($this->id());
		}
	}

	public function get_delete() {
		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
			$bone = Backbone::instance();
			$KAS = KAS::instance();
			$db = $KAS->db();

			$id = intval($_GET['id']);

			$game = $this->game($id);

			if($game->id == $id) {
				$data = $db->query("DELETE FROM games WHERE id=".$id." LIMIT 1");
				if($db->affected_rows()) {
					$bone->delete_file("../".$KAS->thumbs_path()."".$data->slug.".".$KAS->thumbs_file_type());
					$bone->delete_file("../".$KAS->files_path()."".$data->slug.".swf");
					// WHAT IF THERE ARE other types than swf?
					$bone->message('Game deleted successfully!');
				} else {
					$bone->message('A problem occured while trying to delete the game!', 'error');
				}
			}
		}
		$bone->redirect($this->id());
	}

	public function list_games($args = null) {
		$default_args = array("limit" => 10, "order_by" => "id", "order" => "desc", "details" => "all");
		if(is_array($args)) {
			$args = array_merge($default_args, $args);
		}
		$list = KAS_List::instance();
		return $list->games($args);
	}

	public function game($data) {
		$list = KAS_list::instance();
		return $list->load_game($data);
	}

	public function get_gameget() {
		
	}

	private function verify_input($data, $optional=null) {
		$KAS = KAS::instance();
		$return = null;
		$fields = array("name", "slug", "description", "instructions", "category", "tags", "walkthrough", "width", "height", "file", "thumb", "status");

		if(is_array($optional)) {
				$fields = array_diff($fields, $optional);
		}

		$fields_exist = true;
		foreach($fields as $field) {
			if(!array_key_exists($field, $data)) {
				$fields_exist = false;
			}
		}

		if($fields_exist) {
			$list = KAS_List::instance();

			$result = new stdClass();

			// Name
			if(!empty($data['name'])) {
				$result->name = $data['name'];
			}

			// Slug
			if(!empty($data['slug']) && preg_match("/^([a-zA-Z0-9_\-])+$/i", $data['slug'])) {
				$result->slug = $data['slug'];
			}

			// Description
			if(!empty($data['description'])) {
				$result->description = $data['description'];
			}

			// Instructions
			if(!(is_array($optional) && array_key_exists('instructions', $optional)) && !empty($data['instructions'])) {
				$result->instructions = $data['instructions'];
			} else {
				$result->instructions = "";
			}

			// Category
			if(!empty($data['category']) && is_numeric($data['category']) && $list->get_category(intval($data['category']), 0) !== null) {
				$result->category = $data['category'];
			}

			// Tags
			if(!empty($data['tags'])) {
				$result->tags = $data['tags'];
			}

			// Walkthrough
			if(!empty($data['walkthrough'])) {
				$result->walkthrough = $data['walkthrough'];
			} else {
				$result->walkthrough = "";
			}

			// Width
			if(is_numeric($data['width']) && intval($data['width']) >= 0 && intval($data['width']) <= 9999) {
				$result->width = intval($data['width']);
			}

			// Height
			if(is_numeric($data['height']) && intval($data['height']) >= 0 && intval($data['height']) <= 9999) {
				$result->height = intval($data['height']);
			}

			// File
			if(!empty($data['file']) && !empty($data['file']['tmp_name'])) {
				$result->file = FileManager::upload($data['file'], "../".$KAS->files_path(), $data['slug']);
				if($result->file === false) {
					$result->file = "";
				}
			} else {
				$result->file = "";
			}

			// Thumb
			if(!empty($data['thumb']) && !empty($data['thumb']['tmp_name'])) {
				$result->thumb = FileManager::upload($data['thumb'], "../".$KAS->thumbs_path(), $data['slug']);
				if($result->thumb === false) {
					$result->thumb = "";
				}
			} else {
				$result->thumb = "";
			}

			// Status
			if(is_numeric($data['status']) && (intval($data['status']) == 0 || intval($data['status']) == 1)) {
				$result->status = intval($data['status']);
			}

			// Return if all good
			$return = $result;
			foreach($fields as $field) {
				if(!property_exists($result, $field)) {
					$return = null;
				}
			}
		}

		return $return;
	}
}

?>