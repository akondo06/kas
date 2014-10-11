<?php
	
class Advertisments extends Section {
	
	public function __construct() {
		# code...
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Advertisments", 
						"submenu" => array(
										"add" => "Add Advertisment"
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
				$db->query("INSERT INTO advertisments(location,status,content) VALUES('".$data->location."', '".$data->status."', '".$data->content."')");

				if($db->affected_rows()) {
					$bone->message('Advertisment added successfully!');
					$bone->redirect($this->id());
				} else {
					$bone->message('A problem occured while trying to add the advertisment!', 'error');
				}
			} else {
				$bone->message('A problem occured while trying to add the advertisment!', 'error');
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
				$ad = $this->verify_input($_POST);

				if($ad !== null) {

					$db->query("UPDATE advertisments SET location='".$ad->location."', status='".$ad->status."', content='".$ad->content."' WHERE id=".$id." LIMIT 1");

					if($db->affected_rows()) {
						$bone->message('Advertisment modified successfully!');
						$bone->redirect($this->id());
					} else {
						$bone->message('A problem occured while trying to modify the advertisment!', 'error');
					}
				} else {
					$bone->message('A field was invalid and the advertisment could not be updated!', 'error');
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

			$data = $db->query("DELETE FROM advertisments WHERE id=".$id." LIMIT 1");

			if($db->affected_rows()) {
				$bone->message('Advertisment deleted successfully!');
			} else {
				$bone->message('A problem occured while trying to delete the advertisment!', 'error');
			}
		}
		$bone->redirect($this->id());
	}

	public function list_ads() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT id, location, status FROM advertisments");
		return $return;
	}

	public function ad($id) {
		$KAS = KAS::instance();
		if(is_numeric($id) && $id > 0) {
			$data = $KAS->query("SELECT * FROM advertisments WHERE id=".$id." LIMIT 1");
			if(is_array($data) && count($data) == 1 && is_object($data[0])) {
				return $data[0];
			}
		}
		return null;
	}

	private function verify_input($data) {
		$return = null;
		if(array_key_exists('location', $data) && array_key_exists('status', $data) && array_key_exists('content', $data)) {
			$result = new stdClass();

			// Location
			if(!empty($data['location']) && preg_match("/^[a-zA-Z0-9\_\-]*$/", $data['location'])) {
				$result->location = $data['location'];
			}

			// Status
			if(!empty($data['status']) && is_numeric($data['status']) && ($data['status'] == 0 || $data['status'] == 1)) {
				$result->status = $data['status'];
			}

			// Content
			if(!empty($data['content']))
			$result->content = $data['content'];

			// Return if all good
			if(property_exists($result, 'location') && property_exists($result, 'status') && property_exists($result, 'content')) {
				$return = $result;
			}
		}

		return $return;
	}
}

?>