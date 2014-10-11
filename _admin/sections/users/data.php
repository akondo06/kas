<?php
	
class Users extends Section {
	private $levels = array();
	
	public function __construct() {
		// Levels ...
		$levels = $this->list_levels();
		foreach ($levels as $level) {
			$this->levels[$level->id] = new stdClass();
			$lvl = $this->levels[$level->id];
			$lvl->name = $level->name;
			$lvl->privileges = $level->privileges;
		}
	}

	public function menu() {
		// Should return an array for the menu:
		$return = array(
						"title" => "Users", 
						"submenu" => array(
										"levels" => "Levels", 
										"privileges" => "Privileges", 
										"add" => "Add User"
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
				$local = $this->user($data->email);
				if($local === null) {
					$db->query("INSERT INTO users(email,password,nickname,access) VALUES('".$data->email."', '".md5($data->password)."', '".$data->nickname."', '".$data->access."')");

					if($db->affected_rows()) {
						$bone->message('User "'.$data->nickname.'" added successfully!');
						$bone->redirect($this->id());
					} else {
						$bone->message('A problem occured while trying to add the user!', 'error');
					}
				} else {
					$bone->message('A user with the email "'.$data->email.'" already exists!', 'error');
				}
			} else {
				$bone->message('A problem occured while trying to add the user!', 'error');
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
				$data = $this->verify_input($_POST, array('password', 'passwordverify'));

				if($data !== null) {
					$local = $this->user($data->email);
					if($local === null || $local->id == $id) {
						if(property_exists($data, 'password')) {
							$password_field = ", password='".md5($data->password)."'";
						} else {
							$password_field = "";
						}
						$db->query("UPDATE users SET email='".$data->email."'".$password_field.", nickname='".$data->nickname."', access='".$data->access."' WHERE id=".$id." LIMIT 1");

						if($db->affected_rows()) {
							$bone->message('User "'.$data->nickname.'" modified successfully!');
							$bone->redirect($this->id());
						} else {
							$bone->message('A problem occured while trying to modify the user!', 'error');
						}
					} else {
						$bone->message('A user with the given email "'.$data->email.'" already exists!', 'error');
					}
				} else {
					$bone->message('A field was invalid and the user could not be updated!', 'error');
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

			$data = $db->query("DELETE FROM users WHERE id=".$id." LIMIT 1");

			if($db->affected_rows()) {
				$bone->message('User deleted successfully!');
			} else {
				$bone->message('A problem occured while trying to delete the user!', 'error');
			}
		}
		$bone->redirect($this->id());
	}

	public function list_users() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT u.id, u.email, u.nickname, l.name AS level FROM users u LEFT JOIN users_levels l ON(u.access=l.id)");
		return $return;
	}

	public function user($data) {
		$KAS = KAS::instance();

		if(is_numeric($data) && $data > 0) {
			$data = $KAS->query("SELECT u.*, l.name AS level FROM users u LEFT JOIN users_levels l ON(u.access=l.id) WHERE u.id=".intval($data)." LIMIT 1");
		} else if(is_string($data)) {
			$data = $KAS->query("SELECT  u.*, l.name AS level FROM users u LEFT JOIN users_levels l ON(u.access=l.id) WHERE u.email='".$data."' LIMIT 1");
		} else {
			$data = null;
		}

		if(is_array($data) && count($data) == 1 && is_object($data[0])) {
			return $data[0];
		}
		return null;
	}

	public function list_levels() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT * FROM users_levels");
		$all_privileges = $this->list_privileges();
		foreach($return as $level) {
			$level->privileges = array_filter($all_privileges, function($e) use (&$level) { return $e->level == $level->id; });
		}

		return $return;
	}

	public function levels() {
		return $this->levels;
	}

	public function list_privileges() {
		$KAS = KAS::instance();

		$return = $KAS->query("SELECT * FROM users_privileges");

		return $return;
	}

	private function verify_input($data, $optional=null) {
		$return = null;
		$fields = array("email", "password", "passwordverify", "nickname", "access");

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
			// Email
			if(!empty($data['email']) && preg_match("/([\w\-\.\_]+\@[\w\-]+\.[\w\-]+)/", $data['email'])) {
				$result->email = $data['email'];
			}

			// Password
			if(!(is_array($optional) && array_key_exists('password', $optional)) && !empty($data['password']) && strlen($data['password']) >= 8 && $data['password'] == $data['passwordverify']) {
				$result->password = $data['password'];
				$result->passwordverify = $data['passwordverify'];
			}

			// Nickname
			if(!empty($data['nickname']) && preg_match("/^[a-zA-Z0-9_.]\w{5,14}$/i", $data['nickname'])) {
				$result->nickname = $data['nickname'];
			}

			// Access
			if(!empty($data['access']) && is_numeric($data['access']) && array_key_exists($data['access'], $this->levels)) {
				$result->access = $data['access'];
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

	// Levels
	public function get_levels() {
		
	}

	public function get_add_level() {
		echo "add";
	}

	public function get_edit_level() {
		echo "edit";
	}

	public function get_delete_level() {
		echo "delete";
	}

	// Privileges
	public function get_privileges() {
		
	}

	public function get_add_privilege() {
		echo "add";
	}

	public function get_edit_privilege() {
		echo "edit";
	}

	public function get_delete_privilege() {
		echo "delete";
	}

}

?>