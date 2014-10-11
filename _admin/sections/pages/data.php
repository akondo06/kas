<?php
	
class Pages extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Pages", 
						"submenu" => array(
										"add" => "Add Page",
									)
						);
		return $return;
	}

	public function content() {
	}

	public function get_add() {
		$bone = Backbone::instance();
		$KAS = KAS::instance();
		$db = $KAS->db();

		if(array_key_exists('submit', $_POST)) {

			// Check if the given data is valid in a way ...
			$data = $this->verify_input($_POST);

			if($data !== null) {
				$local = $this->page($data->slug);
				if($local === null) {
					$db->query("INSERT INTO pages(title,slug,status,content) VALUES('".$data->title."', '".$data->slug."', '".$data->status."', '".$data->content."')");

					if($db->affected_rows()) {
						$bone->message('Page "'.$data->title.'" added successfully!');
						$bone->redirect($this->id());
					} else {
						$bone->message('A problem occured while trying to add the page!', 'error');
					}
				} else {
					$bone->message('A page with the slug "'.$data->slug.'" already exists!', 'error');
				}
			} else {
				$bone->message('A problem occured while trying to add the page!', 'error');
			}
		}
	}

	public function get_edit() {
		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
			$bone = Backbone::instance();
			$KAS = KAS::instance();
			$db = $KAS->db();

			$id = intval($_GET['id']);

			if(array_key_exists('submit', $_POST)) {

				// Check if the given data is valid in a way ...
				$data = $this->verify_input($_POST);

				if($data !== null) {
					$local = $this->page($data->slug);
					if($local === null || $local->id == $id) {
						$db->query("UPDATE pages SET title='".$data->title."', slug='".$data->slug."', status='".$data->status."', content='".$data->content."' WHERE id=".$id." LIMIT 1");

						if($db->affected_rows()) {
							$bone->message('Page "'.$data->title.'" modified successfully!');
							$bone->redirect($this->id());
						} else {
							$bone->message('A problem occured while trying to modify the page!', 'error');
						}
					} else {
						$bone->message('A page with the given slug "'.$data->slug.'" already exists!', 'error');
					}
				} else {
					$bone->message('A field was invalid and the page could not be updated!', 'error');
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

			$data = $db->query("DELETE FROM pages WHERE id=".$id." LIMIT 1");

			if($db->affected_rows()) {
				$bone->message('Page deleted successfully!');
			} else {
				$bone->message('A problem occured while trying to delete the page!', 'error');
			}
		}
		$bone->redirect($this->id());
	}

	public function list_pages() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT id, title, slug, status FROM pages");
		return $return;
	}

	public function page($data) {
		$KAS = KAS::instance();

		if(is_numeric($data) && $data > 0) {
			$data = $KAS->query("SELECT * FROM pages WHERE id=".intval($data)." LIMIT 1");
		} else if(is_string($data)) {
			$data = $KAS->query("SELECT * FROM pages WHERE slug='".$data."' LIMIT 1");
		} else {
			$data = null;
		}

		if(is_array($data) && count($data) == 1 && is_object($data[0])) {
			return $data[0];
		}
		return null;
	}

	private function verify_input($data) {
		$return = null;
		if(array_key_exists('slug', $data) && array_key_exists('status', $data) && array_key_exists('content', $data)) {
			$result = new stdClass();
			// Title
			if(!empty($data['title'])) {
				$result->title = $data['title'];
			}

			// Slug
			if(!empty($data['slug']) && preg_match("/^[a-zA-Z0-9\_\-]*$/", $data['slug'])) {
				$result->slug = $data['slug'];
			}

			// Status
			if(is_numeric($data['status']) && (intval($data['status']) == 0 || intval($data['status']) == 1)) {
				$result->status = intval($data['status']);
			}

			// Content
			if(!empty($data['content']))
			$result->content = $data['content'];

			// Return if all good
			if(property_exists($result, 'title') && property_exists($result, 'slug') && property_exists($result, 'status') && property_exists($result, 'content')) {
				$return = $result;
			}
		}

		return $return;
	}
}

?>