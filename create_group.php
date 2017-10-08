
<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['name'])) {
	$name = $_POST['name'];
	
	// check if group by name  already existed
	if ($db->isGroupExisted($name)) {
		// exists already
		$response["error"] = TRUE;
		$response["error_msg"] = "Group already existed with " . $name;
		echo json_encode($response);
	} else {
		// create a new group 
		$group = $db->storeGroup($name);
		if ($group) {
			//success
			$response["error"] = FALSE;
			$response["guid"] = $group["unique_id"];
			$response["group"]["name"] = $group["name"];
			$response["group"]["created_at"] = $group["created_at"];
			$response["group"]["updated_at"] = $group["updated_at"];
			echo json_encode($response);
		} else {
			//failed
			$response["error"] = TRUE;
			$response["error_msg"] = "Unknown error occurred in group creation";
			echo json_encode($response);
		}
	}
	
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters (name) is missing!";
	echo json_encode($response);
}
?>
