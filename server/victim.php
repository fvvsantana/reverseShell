<?php
    sleep(5);
    echo "eae gente";

	include('modules/sessionManager.php');
	include('modules/databaseManager.php');

    function main(){
        # Connect to database
        $databaseManager = new DatabaseManager();
        $conn = $databaseManager->connectToDatabase();

		# Start session control
        $sessionManager = new SessionManager($conn);
        $sessionManager->startSessionControl();

		# Check if the session is already stored
		if($sessionManager->isSessionStored("victim")){

        }else{
            echo "Your user is not logged. First login, then enter this page.";
            // 401 Unauthorized
            http_response_code(401);
        }

        # Disconnect from database
        $databaseManager->disconnectFromDatabase();
    }

    main();

?>
