<?php

    function listVictims(){

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
