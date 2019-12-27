<?php
    class SessionManager{
        # The maximum time that a session will be openned
        const SESSION_MAX_AGE = 60*60*24; # 1 day
        const INTERVAL_BETWEEN_SESSION_DELETIONS = 60*60*24; # 1 day

        private $conn;

        # Receive database connection
        function __construct($connectionToDatabase){
            $this->conn = $connectionToDatabase;
        }

        # Update Last Seen of user
        private function updateLastSeen(){
            # Save id of current session
            $currentSessionId = session_id();

            $sql = "UPDATE ATTACKERS SET LAST_SEEN=NOW() WHERE SESSION_ID='$currentSessionId'";
    		# Check query error
    		if(!mysqli_query($this->conn, $sql)){
    			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
    		}

            $sql = "UPDATE VICTIMS SET LAST_SEEN=NOW() WHERE SESSION_ID='$currentSessionId'";
    		# Check query error
    		if(!mysqli_query($this->conn, $sql)){
    			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
    		}
        }

        /*
        Return true if the last deletion of old sessions occurred at least an
        interval of time of INTERVAL_BETWEEN_SESSION_DELETIONS ago.
        That is, return true if it's time to delete old sessions.
        */
        private function shouldDeleteOldSessions(){
            $sql = "SELECT * FROM UTILS
                    WHERE TIMESTAMPDIFF(SECOND, LAST_DELETION_OF_SESSIONS, NOW()) > " . self::INTERVAL_BETWEEN_SESSION_DELETIONS;
    		$result = mysqli_query($this->conn, $sql);
            #print_r($result);
    		return mysqli_num_rows($result);
        }

        # Delete old sessions from database
        private function deleteOldSessions(){
            # DEBUG
            echo 'DEBUG: Deleting old sessions';

            # Save id of current session
            $currentSessionId = session_id();
            # Delete old sessions of attackers, but not the current session
            $sql = "DELETE FROM ATTACKERS
                    WHERE (TIMESTAMPDIFF(SECOND, LAST_SEEN, NOW()) > " . self::SESSION_MAX_AGE . ") AND
                    (SESSION_ID != '$currentSessionId')";
    		# Check query error
    		if(!mysqli_query($this->conn, $sql)){
    			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
    		}

            # Delete old sessions of victims, but not the current session
            $sql = "DELETE FROM VICTIMS
                    WHERE (TIMESTAMPDIFF(SECOND, LAST_SEEN, NOW()) > " . self::SESSION_MAX_AGE . ") AND
                    (SESSION_ID != '$currentSessionId')";
    		# Check query error
    		if(!mysqli_query($this->conn, $sql)){
    			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
    		}

            # Update timestamp of last deletion
            $sql = "UPDATE UTILS SET LAST_DELETION_OF_SESSIONS=NOW()";
    		# Check query error
    		if(!mysqli_query($this->conn, $sql)){
    			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
    		}
        }

        /*
            Load session cookie.
            Update last seen of user on database.
            Check if it's time to delete old sessions from database.
        */
        function startSessionControl(){
        	# Start user session
        	session_start();

            # Update Last Seen of current user, only if user already is in the
            # table.
            $this->updateLastSeen();

            # If it's time to delete old sessions
            if($this->shouldDeleteOldSessions()){
                # Delete old sessions from database
                $this->deleteOldSessions();
            }

        }

    	/*
    	 * Return the number of rows where current session id matches the column
    	 * SESSION_ID in the table.
    	 * If type == 'attacker' it will search inside of ATTACKERS table.
    	 * If type == 'victim' it will search inside of VICTIMS table.
    	*/
    	function isCurrentSessionStored($type){
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
    		$sql = "SELECT SESSION_ID FROM $table WHERE SESSION_ID='$sessionId'";
    		$result = mysqli_query($this->conn, $sql);

    		# Return number of rows in result
    		return mysqli_num_rows($result);
    	}

    	/*
    	Store user information (session) into database. It's stored as a row in
    	the table "VICTIMS" or "ATTACKERS", containing: Session ID, IP, PORT, Host
    	Name and Timestamp of last appearance.
    	*/
    	function storeCurrentSession($type){
    		# Choose right table
    		$table = '';
    		if($type == 'attacker'){
    			$table = 'ATTACKERS';
    		}elseif($type == 'victim'){
    			$table = 'VICTIMS';
    		}

			# Prepare query
			$sessionId = mysqli_real_escape_string($this->conn, session_id());
			$ip = mysqli_real_escape_string($this->conn, $_SERVER['REMOTE_ADDR']);
			$port = mysqli_real_escape_string($this->conn, $_SERVER['REMOTE_PORT']);
			$host = mysqli_real_escape_string($this->conn, $_SERVER['REMOTE_HOST'] ?? '');
			$sql = "INSERT INTO $table(SESSION_ID, IP, PORT, HOST, LAST_SEEN)
					VALUES ('$sessionId', '$ip', '$port', '$host', NOW())";

			# Check query error
			if(mysqli_query($this->conn, $sql)){
				return True;
			}else{
				echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($this->conn) . '\n';
                # 500 Internal Server Error
                http_response_code(500);
				return False;
			}

    	}

    }
?>
