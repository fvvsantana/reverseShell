<?php
    #echo $_POST['cmd'];
	$types = [];
	session_start();
	print_r($_SESSION);
	print_r($GLOBALS);
	if(isset($_POST['cmd'])){
		echo $_POST['cmd'];
	}
?>
