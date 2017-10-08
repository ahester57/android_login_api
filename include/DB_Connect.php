
<?php

class DB_Connect {
	private $conn;
	
	// connect to db
	public function connect() {
		require_once 'include/Config.php';
		
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		return $this->conn;
	}
}
?>
