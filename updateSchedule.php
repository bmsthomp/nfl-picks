<?php 
	session_start();
	require 'connection.php';
	require 'weekCalculator.php';

	var_dump($_POST);
	$tbl_name = "schedule";

	unset($_POST['submit']);

	for ($i=0; $i < 16; $i++) { 
		if ($_POST["ateam$i"] && $_POST["hteam$i"]){
			echo "\nthis will be an insert " + $_POST["ateam$i"];
		} else {
			echo "this will be an udpate " + $_POST["ascore$i"];
		}	
	}
?>

