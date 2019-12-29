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
		if($sessionManager->isCurrentSessionStored("attacker")){
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
                $commandOutput = "";
                while($out = socket_read($msgsock, 2048)) {
                    $commandOutput .= $out;
                }

				echo $commandOutput;

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
				/*
				sleep(3);


				# Connect to victim
				$result = false;
				while(!$result){
					try{
		                $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		                if($sock === false) {
		                    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		                    # 500 Internal Server Error
		                    http_response_code(500);
							return 1;
		                }

		                $result = @socket_connect($sock, $address, $port);
					}catch(Exception $e){
						# Close connection
		                socket_close($sock);
						sleep(1);
						echo "ONE MORE SOCKET FAILED";
					}
				}
				*/

				#include("../modules/socket_connect_timeout.php");

				# Connect to victim
				/*
				try{
	                $sock = @socket_connect_timeout($address, $port);
				}catch(Exception $e){

				}
				*/
				/*
                if ($result === false) {
                    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
                }
				*/

				/*
				socket_set_nonblock($sock);

		        $error = NULL;
		        $attempts = 0;
		        $timeout = 30;  // adjust because we sleeping in 1 millisecond increments
		        $connected;
		        while (!($connected = @socket_connect($sock, $remote, $port+0)) && $attempts++ < $timeout) {
		            $error = socket_last_error();
		            if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY) {
		                echo "Error Connecting Socket: " . socket_strerror($error) . "\n";
		                socket_close($sock);
	                    # 500 Internal Server Error
	                    http_response_code(500);
		                return 1;
		            }
		            usleep(1000);
		        }

		        if (!$connected) {
		            echo "Error Connecting Socket: Connect Timed Out After $timeout seconds." . socket_strerror(socket_last_error());
		            socket_close($sock);
                    # 500 Internal Server Error
                    http_response_code(500);
	                return 1;
		        }

		        socket_set_block($sock);
				*/

				/*
				include("../modules/socket_connect_timeout.php");
                #$sock = socket_connect_timeout($address, $port, 10);

				$sock = false;
				while(!$sock){
					usleep(5000);
					# Disable warnings
					error_reporting(E_ALL ^ E_WARNING);
					try{
						# Try to connect to victim
		                $sock = socket_connect_timeout($address, $port, 10);
					}catch(Exception $e){

					}
					# Enable warnings
					error_reporting(E_ALL);
				}
				*/

				/*
				sleep(5);
				# Disable warnings
				#error_reporting(E_ALL ^ E_WARNING);
				# Try to connect to victim
            	$result = socket_connect($sock, $address, $port);
				# Enable warnings
				#error_reporting(E_ALL);
				*/
				/*
				$result = socket_connect($sock, $address, $port);
				$result = socket_connect($sock, $address, $port);
				$result = socket_connect($sock, $address, $port);
				echo "Value of result: $result\n";
				echo "Value of !result: " . !$result . "\n";
				echo "first result\n";
				sleep(5);
				$result = socket_connect($sock, $address, $port);
				echo "second result\n";
				echo "Value of result: $result\n";
				echo "Value of !result: " . !$result . "\n";
				*/
				/*
                if ($result === false) {
                    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($sock)) . "\n";
                    # 500 Internal Server Error
                    http_response_code(500);
                }
				*/
				/*
				Lições aprendidas:
Faça varias vezes o bind e o connect até dar certo. O bind pode dar errado pq demora pra liberar a porta. O connect pode dar errado pq o server não está aberto ainda.
Projete sua aplicação de forma que o client inicie o fechamento de conexão. Se o server que tiver que iniciar o fechamento de conexão ele vai ter o time_wait:
https://stackoverflow.com/questions/3757289/when-is-tcp-option-so-linger-0-required
E assim, o server vai demorar pra liberar a porta.
O jeito que eu fiz foi: o client escrevia e o server lia, logo depois de o client escrever ele fechava conexão. Assim, até o server ler e fechar a conexão, o client já tinha fechado, aí não demora. Mesmo assim mative os whiles pro bind e pro connect
*/

?>
