<?php
    # Debug flag to turn on/off debug features
	define("DEBUG", 1);
    # Interval between reconnections and rebindings on socket (microseconds)
	define("SOCKET_RECONNECT_INTERVAL", 500000);
    # Maximum number of times that we'll try to reconnect socket
	define("SOCKET_RECONNECT_MAX_ATTEMPT_NUMBER", 0);
    # Interval between reconnections and rebindings on socket (microseconds)
	define("SOCKET_BIND_INTERVAL", 500000);
    # Maximum number of times that we'll try to reconnect socket
	define("SOCKET_BIND_MAX_ATTEMPT_NUMBER", 0);

	/*
		Try to connect to server multiple times.
		If $nAttempts == 0, retry to connect until make it.
		If $nAttempts > 0, $nAttempts means the maximum number of attempts to
		connect that will be done.
		Return the final result of connection.
		If the flag $debug == 1, show debug messages from connection errors.
		The parameter $intervalBetweenAttempts is the interval in microseconds
		between each attempt to connect to server.
	*/
	function socket_connect_loop($sock, $address, $port,
				$intervalBetweenAttempts, $nAttempts, $debug){
		if(DEBUG){
			# Try to connect to server
        	$result = socket_connect($sock, $address, $port);
			# Count number of attempts to connect
			$currentNAttempts = 1;

			while(!$result &&
					(!$nAttempts || ($currentNAttempts <= $nAttempts))
			){
				usleep($intervalBetweenAttempts);
				# Try to connect to server
            	$result = socket_connect($sock, $address, $port);
				# Count number of attempts to connect
				$currentNAttempts++;
			}

		}else{
			# Disable warnings
			error_reporting(E_ALL ^ E_WARNING);
			# Try to connect to server
        	$result = socket_connect($sock, $address, $port);
			# Enable warnings
			error_reporting(E_ALL);
			# Count number of attempts to connect
			$currentNAttempts = 1;

			while(!$result &&
					(!$nAttempts || ($currentNAttempts <= $nAttempts))
			){
				usleep($intervalBetweenAttempts);
				# Disable warnings
				error_reporting(E_ALL ^ E_WARNING);
				# Try to connect to server
            	$result = socket_connect($sock, $address, $port);
				# Enable warnings
				error_reporting(E_ALL);
				# Count number of attempts to connect
				$currentNAttempts++;
			}

		}
		return $result;
	}

	/*
		Try to bind to port multiple times.
		If $nAttempts == 0, retry to bind until make it.
		If $nAttempts > 0, $nAttempts means the maximum number of attempts to
		bind that will be done.
		Return the final result of binding.
		If the flag $debug == 1, show debug messages from binding errors.
		The parameter $intervalBetweenAttempts is the interval in microseconds
		between each attempt to bind to port.
	*/
	function socket_bind_loop($sock, $address, $port,
				$intervalBetweenAttempts, $nAttempts, $debug){
		if(DEBUG){
			# Try to bind to port
        	$result = socket_bind($sock, $address, $port);
			# Count number of attempts to connect
			$currentNAttempts = 1;

			while(!$result &&
					(!$nAttempts || ($currentNAttempts <= $nAttempts))
			){
				usleep($intervalBetweenAttempts);
				# Try to bind to port
	        	$result = socket_bind($sock, $address, $port);
				# Count number of attempts to connect
				$currentNAttempts++;
			}

		}else{
			# Disable warnings
			error_reporting(E_ALL ^ E_WARNING);
			# Try to bind to port
        	$result = socket_bind($sock, $address, $port);
			# Enable warnings
			error_reporting(E_ALL);
			# Count number of attempts to connect
			$currentNAttempts = 1;

			while(!$result &&
					(!$nAttempts || ($currentNAttempts <= $nAttempts))
			){
				usleep($intervalBetweenAttempts);
				# Disable warnings
				error_reporting(E_ALL ^ E_WARNING);
				# Try to bind to port
	        	$result = socket_bind($sock, $address, $port);
				# Enable warnings
				error_reporting(E_ALL);
				# Count number of attempts to connect
				$currentNAttempts++;
			}

		}
		return $result;
	}

?>
