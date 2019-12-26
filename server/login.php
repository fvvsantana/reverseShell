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
			if(!$sessionManager->isSessionStored($type)){
				# Store the current session
				if($sessionManager->storeSession($type)){
					header("Location: $type.php");
				}
			}
		}

        # Disconnect from database
        $databaseManager->disconnectFromDatabase();

	}

	main();

?>
