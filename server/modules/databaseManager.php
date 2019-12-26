<?php
	class DatabaseManager{
		private $conn;

		function connectToDatabase(){
			$this->conn = mysqli_connect('localhost', 'userTest', 'passwordTest', 'kellyclarkson');
			if(!$this->conn){
				echo 'Connection error: ' . mysqli_connect_error();
                # 500 Internal Server Error
                http_response_code(500);
			}
			return $this->conn;
		}

		function disconnectFromDatabase(){
	    	# Close database connection
	    	mysqli_close($this->conn);
		}

		function getConnection(){
			return $this->conn;
		}

	}
?>
