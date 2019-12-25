<?php
	/*
	# Function for debugging
	function saveDebug($debugString){
		global $myErrors;
		$myErrors = $myErrors . $debugString . '\n';
	}

	# Function for debugging
	function showDebug(){
		return 'DEBUG: ' . $GLOBALS['myErrors'];
	}
	*/

	/*
	 * Return true if session is stored in the database.
	 * If type == 'attacker' it will search inside of ATTACKERS table.
	 * If type == 'victim' it will search inside of VICTIMS table.
	*/
	function isSessionStored($connection, $type){
		# Choose right table
		$table = '';
		if($type == 'attacker'){
			$table = 'ATTACKERS';
		}elseif($type == 'victim'){
			$table = 'VICTIMS';
		}

		# Get id of current session
		$sessionId = session_id();

		# Search for session id inside of the chosen table
		$sql = "SELECT SESSION_ID FROM $table WHERE SESSION_ID=$sessionId";
		$result = mysqli_query($connection, $sql);

		#saveDebug('Result is: ' . print_r($result) . '\n');
		return mysqli_num_rows($result);
	}

	#
	function storeSession($connection, $type){
		if($type == 'victim'){
			# Prepare query
			$sessionId = mysqli_real_escape_string($connection, session_id());
			$ip = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR']);
			$port = mysqli_real_escape_string($connection, $_SERVER['REMOTE_PORT']);
			$host = mysqli_real_escape_string($connection, $_SERVER['REMOTE_HOST'] ?? '');
			$sql = "INSERT INTO VICTIMS(SESSION_ID, IP, PORT, HOST, LAST_SEEN)
					VALUES ('$sessionId', '$ip', '$port', '$host', NOW())";

			# Check query error
			if(mysqli_query($connection, $sql)){
				return True;
			}else{
				echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($connection) . '\n';
				return False;
			}

		}elseif($type == 'attacker'){
			# Prepare query
			$sessionId = mysqli_real_escape_string($connection, session_id());
			$ip = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR']);
			$port = mysqli_real_escape_string($connection, $_SERVER['REMOTE_PORT']);
			$host = mysqli_real_escape_string($connection, $_SERVER['REMOTE_HOST'] ?? '');
			$sql = "INSERT INTO ATTACKERS(SESSION_ID, IP, PORT, HOST, LAST_SEEN)
					VALUES ('$sessionId', '$ip', '$port', '$host', NOW())";

			# Check query error
			if(mysqli_query($connection, $sql)){
				return True;
			}else{
				echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($connection) . '\n';
				return False;
			}
		}
	}

	function main(){
		include('templates/header.php');

		# Check if a login was requested
		if(isset($_GET['login'], $_GET['type']) && $_GET['login'] == 1){
			# Get type. It can be 'attacker' or 'victim'.
			$type = $_GET['type'];

			# Check if the session is already stored
			if(!isSessionStored($conn, $type)){
				#echo showDebug();
				# Store the current session
				if(storeSession($conn, $type)){
					header("Location: $type.php");
				}
			}
		}

		# Close db connection
		include('templates/footer.php');
	}

	main();

?>
