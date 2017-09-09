<?php

	$host="localhost"; // Host name 
	$username=""; // Mysql username 
	$password=""; // Mysql password
	$db_name="slahsdot_picks"; // Database name 

	mysql_connect("$host", "$username", "$password")or die("cannot connect: " . mysqli_connect_error()); 
	mysql_select_db("$db_name")or die("cannot select DB");

?>