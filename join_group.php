
<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if(isset($_POST['user_id']) && isset($_POST['group_id'])) {

	$uid = $_POST['user_id'];
	$guid = $_POST['group_id'];

	
	$user_group = $db->addUserToGroup($uid, $guid);
	
	if ($user_group != false) {
		//user added 
		$response["error"] = FALSE;
		$response["id"] = $user_group["id"];
		$response["user_group"]["user_id"] = $user_group["user_uid"];
		$response["user_group"]["group_id"] = $user_group["group_uid"];
		$response["user_group"]["created_at"] = $user_group["created_at"];
		$response["user_group"]["updated_at"] = $user_group["updated_at"];
		echo json_encode($response);
	} else {
		// user notadded 
		$response["error"] = TRUE;
		$response["error_msg"] = "Wrong. Try better next time, please.";
		echo json_encode($response);
	}
} else {
	// params missing
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters user_id or group_id is missing!";
	echo json_encode($response);
}

?>
