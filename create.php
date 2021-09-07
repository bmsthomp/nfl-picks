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

		$uid = mysqli_real_escape_string($con, $uid);
		$fname = mysqli_real_escape_string($con, $fname);
		$lname = mysqli_real_escape_string($con, $lname);
		$password = mysqli_real_escape_string($con, $password);

		$sql_check = mysqli_query($con, "SELECT uid FROM users WHERE uid ='$uid';");
		mysqli_close($con);
		$sql_check = mysqli_num_rows($sql_check);

		// the uid is unique
		if ($sql_check==0){ 

			$sql = "INSERT INTO users (uid, fname, lname, pw) VALUES ('$uid', '$fname', '$lname', MD5('$password'));";
			$result = mysqli_query($con, $sql);

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