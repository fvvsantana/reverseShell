<?php
    # The maximum time that a session will be openned
    define('SESSION_MAX_AGE', 60*60*24); # 1 day

    # The maximum time that a session will be openned
    #define('INTERVAL_BETWEEN_SESSION_DELETIONS', 60*60*24); # 1 day
    define('INTERVAL_BETWEEN_SESSION_DELETIONS', 1); # 1 day

    # Update Last Seen of user
    function updateLastSeen($conn){
        # Save id of current session
        $currentSessionId = session_id();

        $sql = "UPDATE ATTACKERS SET LAST_SEEN=NOW() WHERE SESSION_ID='$currentSessionId'";
		# Check query error
		if(!mysqli_query($conn, $sql)){
			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($conn) . '\n';
		}

        $sql = "UPDATE VICTIMS SET LAST_SEEN=NOW() WHERE SESSION_ID='$currentSessionId'";
		# Check query error
		if(!mysqli_query($conn, $sql)){
			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($conn) . '\n';
		}
    }

    # Return true if has passed at least INTERVAL_BETWEEN_SESSION_DELETIONS
    # since the last deletion of sessions. Else, return false.
    function shouldDeleteOldSessions($conn){
        $sql = "SELECT * FROM UTILS
                WHERE TIMESTAMPDIFF(SECOND, LAST_DELETION_OF_SESSIONS, NOW()) > " . INTERVAL_BETWEEN_SESSION_DELETIONS;
		$result = mysqli_query($conn, $sql);
        print_r($result);
		return mysqli_num_rows($result);
    }
    // ?????????????????????????????????????????????????????????????????????????????????
    // Boa noite

    # Delete old sessions from database
    function deleteOldSessions($conn){
        # DEBUG
        echo 'DEBUG: Deleting old sessions';

        # Save id of current session
        $currentSessionId = session_id();
        # Delete old sessions of attackers, but not the current session
        $sql = "DELETE FROM ATTACKERS
                WHERE (TIMESTAMPDIFF(SECOND, LAST_SEEN, NOW()) > " . SESSION_MAX_AGE . ") AND
                (SESSION_ID != '$currentSessionId')";
		# Check query error
		if(!mysqli_query($conn, $sql)){
			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($conn) . '\n';
		}

        # Delete old sessions of victims, but not the current session
        $sql = "DELETE FROM VICTIMS
                WHERE (TIMESTAMPDIFF(SECOND, LAST_SEEN, NOW()) > " . SESSION_MAX_AGE . ") AND
                (SESSION_ID != '$currentSessionId')";
		# Check query error
		if(!mysqli_query($conn, $sql)){
			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($conn) . '\n';
		}

        # Update timestamp of last deletion
        $sql = "UPDATE UTILS SET LAST_DELETION_OF_SESSIONS=NOW()";
		# Check query error
		if(!mysqli_query($conn, $sql)){
			echo "SQL query error:\n Query: $sql \n Error: " . mysqli_error($conn) . '\n';
		}

    }


	# Start user session
	session_start();

	# Connect to database
	include('config/db_connect.php');

    # Update Last Seen of user
    updateLastSeen($conn);

    if(shouldDeleteOldSessions($conn)){
        echo 'ai meu cu';
        # Delete old sessions from database
        deleteOldSessions($conn);
    }
?>
