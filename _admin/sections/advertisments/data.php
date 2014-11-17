<?php
	
class Advertisments extends Section {
	public function __construct() {
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

		if(array_key_exists('submit', $_POST)) {
			$model = new AdvertismentRow($_POST);

			$result = $model->save();

			if($result === true) {
				$bone->message('Advertisment added successfully!');
				$bone->redirect($this->id());
			} else {
				$errors = implode(' ', $result);
				$bone->message($errors, 'error');
			}
		}
	}

	public function get_edit() {
		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
			$bone = Backbone::instance();

			$id = intval($_GET['id']);

			if(array_key_exists('submit', $_POST)) {
				$data = $_POST;
				$data['id'] = $id;

				$model = new AdvertismentRow($data);
				$save = $model->save();

				if($save === true) {
					$bone->message('Advertisment modified successfully!');
					$bone->redirect($this->id());
				} else if(is_array($save)){
					$errors = implode(' ', $save);
					$bone->message($errors, 'error');
				} else {
					$bone->message('Nothing to change or could not save the changes.', 'info');
					$bone->redirect($this->id());
				}
			}

		} else {
			$bone->redirect($this->id());
		}
	}

	public function get_delete() {
		if(array_key_exists('id', $_GET) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
			$bone = Backbone::instance();

			$id = intval($_GET['id']);

			$model = new AdvertismentRow(array('id'=> $id));
			$action = $model->delete();

			if($action === true) {
				$bone->message('Advertisment deleted successfully!');
			} else {
				$bone->message("A problem occured while trying to delete the advertisment!", 'error');
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
		if(is_numeric($id) && $id > 0) {
			$model = new AdvertismentRow();
			return (object) $model->get($id);
		}
		return null;
	}
}

?>