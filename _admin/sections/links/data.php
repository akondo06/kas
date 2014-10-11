<?php
	
class Links extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Links", 
						"submenu" => array(
										"add" => "Add Link",
										"check" => "Check All Links"
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
				$local = $this->link($data->url);
				if($local === null) {
					$db->query("INSERT INTO links(anchor,description,url,contact,status) VALUES('".$data->anchor."', '".$data->description."', '".str_replace('http://', '', $data->url)."', '".$data->contact."', '".$data->status."')");

					if($db->affected_rows()) {
						$bone->message('Link to "'.$data->url.'" added successfully!');
						$bone->redirect($this->id());
					} else {
						$bone->message('A problem occured while trying to add the link!', 'error');
					}
				} else {
					$bone->message('A link with the url "'.$data->url.'" already exists!', 'error');
				}
			} else {
				$bone->message('A problem occured while trying to add the link!', 'error');
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
					$local = $this->link($data->url);
					var_dump($local);
					if($local === null || $local->id == $id) {
						if(!$bone->object_has_fields($data, $local)) {
							$db->query("UPDATE links SET anchor='".$data->anchor."', description='".$data->description."', url='".str_replace('http://', '', $data->url)."', contact='".$data->contact."', status='".$data->status."' WHERE id=".$id." LIMIT 1");

							if($db->affected_rows()) {
								$bone->message('Link "'.$data->anchor.'" modified successfully!');
								$bone->redirect($this->id());
							} else {
								$bone->message('A problem occured while trying to modify the link!', 'error');
							}
						} else {
							$bone->message('Nothing to change!', 'warning');
						}
					} else {
						$bone->message('A link with the given url "'.$data->url.'" already exists!', 'error');
					}
				} else {
					$bone->message('A field was invalid and the link could not be updated!', 'error');
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

			$data = $db->query("DELETE FROM links WHERE id=".$id." LIMIT 1");

			if($db->affected_rows()) {
				$bone->message('Link deleted successfully!');
			} else {
				$bone->message('A problem occured while trying to delete the link!', 'error');
			}
		}
		$bone->redirect($this->id());
	}

	public function get_check() {
		$bone = Backbone::instance();
		$KAS = KAS::instance();
		$db = $KAS->db();

		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {

			$id = intval($_GET['id']);

			$data = $this->link($id);

			if($data !== null && is_object($data) && property_exists($data, 'url')) {
				if($this->verify_url($data->url)) {
					// SET THE STATUS TO 1 HERE ...
					$bone->message('Successfully checked '.$data->url.'.');
				} else {
					$bone->message(''.$data->url.' does not have the link on its pages or the site is down. Please check manually.', 'error');
				}
			} else {
				// SET THE STATUS TO 0 HERE ...
				$bone->message('Given URL is Invalid or non-existent!', 'error');
			}
		} else {
			$bone->message('Successfully checked all links');
		}
		//Maybe STORE results in SESSION?
		$bone->redirect($this->id());
	}

	public function list_links() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT id, anchor, description, url, contact, status FROM links");
		return $return;
	}

	public function link($data) {
		$KAS = KAS::instance();

		if(is_numeric($data) && $data > 0) {
			$data = $KAS->query("SELECT * FROM links WHERE id=".intval($data)." LIMIT 1");
		} else if(is_string($data)) {
			$data = $KAS->query("SELECT * FROM links WHERE url='".$data."' LIMIT 1");
		} else {
			$data = null;
		}

		if(is_array($data) && count($data) == 1 && is_object($data[0])) {
			return $data[0];
		}
		return null;
	}

	private function verify_url($url) {
		return true;
	}

	private function verify_input($data, $optional=null) {
		$return = null;
		$fields = array("anchor", "description", "url", "contact", "status");

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
			$result = new stdClass();

			// Anchor
			if(!empty($data['anchor']) && preg_match("/^[a-zA-Z\s\-\.\!]*$/i", $data['anchor'])) {
				$result->anchor = $data['anchor'];
			}

			// Description
			if(!empty($data['description']) && preg_match("/^[a-zA-Z\s\-\.\!]*$/i", $data['description'])) {
				$result->description = $data['description'];
			}

			// Url
			if(!empty($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL)) {
				$result->url = $data['url'];
			}

			// Contact
			if(!empty($data['contact']) && preg_match("/([\w\-\.\_]+\@[\w\-]+\.[\w\-]+)/", $data['contact'])) {
				$result->contact = $data['contact'];
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