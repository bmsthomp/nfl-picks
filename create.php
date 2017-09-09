<?php
	session_start();

	// username and password sent from form 
	$uid=$_POST['uid'];
	$fname=$_POST['fname']; 
	$lname=$_POST['lname'];
	$password=$_POST['password'];

	// if there is no value for any input return to signup.php
	if ($fname and $lname and $password){

		require 'connection.php';

		$uid = mysql_real_escape_string(stripslashes($uid));
		$fname = mysql_real_escape_string(stripslashes($fname));
		$lname = mysql_real_escape_string(stripslashes($lname));
		$password = mysql_real_escape_string(stripslashes($password));

		$sql_check = mysql_query("SELECT uid FROM users WHERE uid ='$uid';");
		$sql_check = mysql_num_rows($sql_check);

		// the uid is unique
		if ($sql_check==0){ 

			$sql = "INSERT INTO users (uid, fname, lname, pw) VALUES ('$uid', '$fname', '$lname', MD5('$password'));";
			$result = mysql_query($sql);

			if (!$result){
				$_SESSION['errors'] = array("Could not create account.");
				header('location:signup.php');

			} else {
				$_SESSION['username'] = $email;
				$_SESSION['password'] = $password;
				header('location:main.php');
			}
		} else {
			$_SESSION['errors'] = array("There is already an account associated with this username.");
			header('location:signup.php');
		}

	} else {header('location:signup.php');}
	
?>