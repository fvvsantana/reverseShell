<?php
	include('modules/sessionManager.php');
	include('modules/databaseManager.php');

	function main(){
        # Connect to database
        $databaseManager = new DatabaseManager();
        $conn = $databaseManager->connectToDatabase();

		# Start session control
        $sessionManager = new SessionManager($conn);
        $sessionManager->startSessionControl();


		# Check if a login was requested
		if(isset($_GET['login'], $_GET['type']) && $_GET['login'] == 1){
			# Get type. It can be 'attacker' or 'victim'.
			$type = $_GET['type'];

			# Check if the session is already stored
			if($sessionManager->isSessionStored($type)){
				echo "Login Successful\n";
				#header("Location: $type.php");
			}else{
				# Store the current session
				if($sessionManager->storeSession($type)){
					echo "Login Successful\n";
					#header("Location: $type.php");
				}else{
					echo "Login failed\n";
	                # 500 Internal Server Error
	                http_response_code(500);
				}
			}

		}

        # Disconnect from database
        $databaseManager->disconnectFromDatabase();

	}

	main();

?>
