<?php

	$host="localhost"; // Host name 
	$username=""; // Mysql username 
	$password=""; // Mysql password
	$db_name="nflpicks"; // Database name 

	$con = new mysqli("$host", "$username", "$password", "$db_name");

	// Check connection
	if ($con -> connect_errno) {
		echo "Failed to connect to MySQL: " . $con -> connect_error;
		exit();
	}
?>