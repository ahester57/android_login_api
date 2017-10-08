
<?php

class DB_Functions {
	private $conn;
	
	function __construct() {
		require_once 'DB_Connect.php';
		// connect to db
		$db = new DB_Connect();
		$this->conn = $db->connect();
	}
	
	function __destruct() {
	
	}

	// store new user
	// returns user details
	public function storeUser($name, $email, $password) {
		$uuid = uniqid('', true);
		$hash = $this->hashSSHA($password);
		$encrypted_password = $hash["encrypted"];
		$salt = $hash["salt"];
		
		$stmt = $this->conn->prepare("INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at) VALUES(?, ?, ?, ?, ?, NOW())");
		$stmt->bind_param("sssss", $uuid, $name, $email, $encrypted_password, $salt);
		$result = $stmt->execute();
		$stmt->close();
		
	// check for success
		if ($result) {
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			
			return $user;
		} else {
			return false;
		}
	}
	

	// create a new group
	public function storeGroup($name) {
		$guid = uniqid('',true);
		$stmt = $this->conn->prepare("INSERT INTO groups(unique_id, name, created_at) VALUES(?, ?, NOW())");

		$stmt->bind_param("ss", $guid, $name);
		$result = $stmt->execute();
		$stmt->close();

		// check fo rsuccess
		if ($result) {
			$stmt = $this->conn->prepare("SELECT * FROM groups WHERE name = ?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			$group = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			return $group;
		} else {
			return false;
		}

	}

	public function addUserToGroup($user_id, $group_id) {
		$stmt = $this->conn->prepare("INSERT INTO user_groups(user_uid, group_uid, created_at) VALUES(?, ?, NOW())");
		$stmt->bind_param("ss", $user_id, $group_id);
		$result = $stmt->execute();
		$stmt->close();

		// check fo rsuccess
		if ($result) {
			$stmt = $this->conn->prepare("SELECT * FROM user_groups WHERE user_uid = ?");
			$stmt->bind_param("s", $user_id);
			$stmt->execute();
			$user_group = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			return $user_group;
		} else {
			return false;
		}
		
	}

	// Get user by email and password
	public function getUserByEmailPassword($email, $password) {
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		
		if ($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			
			//verifying user password
			$salt = $user['salt'];
			$encrypted_password = $user['encrypted_password'];
			$hash = $this->checkhashSSHA($salt, $password);
			if ($encrypted_password == $hash) {
				return $user;
			}
		} else {
			return NULL;
		}
	}
	
	//does user exist?
	public function isUserExisted($email) {
		$stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		
		if ($stmt->num_rows > 0) {
			$stmt->close();
			return true;
		} else {
			$stmt->close();
			return false;
		}
	}

	// does group exist?
	public function isGroupExisted($name) {
		$stmt = $this->conn->prepare("SELECT name FROM groups WHERE name = ?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->close();
			return true;
		} else {
			$stmt->close();
			return false;
		}
	}

	public function getAllUsersFromGroup($group_id) {
		$stmt = $this->conn->prepare("SELECT * FROM user_groups WHERE id = ?");
		$stmt->bind_param("i", $group_id);
		$stmt->execute();
		$users = $stmt->get_result();
		$stmt->close();
		return $users;
	}

	// get group id from name
	public function getGroupId($name) {
		$stmt = $this->conn->prepare("SELECT unique_id FROM groups WHERE name = ?");
		$stmt->bind_param("s", $name);
		if ($stmt->execute()) {
			$group_id = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $group_id;
		} else {
			return NULL;
		}
	}
	

	
	//encrypt password
	public function hashSSHA($password) {
		$salt = sha1(rand());
		$salt = substr($salt, 0, 10);
		$encrypted = base64_encode(sha1($password . $salt, true) . $salt);
		$hash = array("salt" => $salt, "encrypted" => $encrypted);
		return $hash;
	}
	
	//decrypt password
	public function checkhashSSHA($salt, $password) {
		$hash = base64_encode(sha1($password . $salt, true) . $salt);
		return $hash;
	}
	
}
?>
