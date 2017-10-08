
<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	// check if user already existed
	if ($db->isUserExisted($email)) {
		// exists already
		$response["error"] = TRUE;
		$response["error_msg"] = "User already existed with " . $email;
		echo json_encode($response);
	} else {
		// create a new user
		$user = $db->storeUser($name, $email, $password);
		if ($user) {
			//success
			$response["error"] = FALSE;
			$response["uid"] = $user["unique_id"];
			$response["user"]["name"] = $user["name"];
			$response["user"]["email"] = $user["email"];
			$response["user"]["created_at"] = $user["created_at"];
			$response["user"]["update_at"] = $user["updated_at"];
			echo json_encode($response);
		} else {
			//failed
			$response["error"] = TRUE;
			$response["error_msg"] = "Unknown error occurred in registration.";
			echo json_encode($response);
		}
	}
	
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters (name, email or password) is missing!";
	echo json_encode($response);
}
?>