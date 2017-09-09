<?php
	session_start();

	require 'connection.php';
	$tbl_name = "users";

	// username and password sent from form 
	$myusername=$_POST['username']; 
	$mypassword=$_POST['password']; 

	// To protect MySQL injection (more detail about MySQL injection)
	$usr = mysql_real_escape_string(stripslashes($myusername));
	$pwd = mysql_real_escape_string(stripslashes($mypassword));

	$sql="SELECT role FROM $tbl_name WHERE uid='$usr' and pw=md5('$pwd')";
	$result=mysql_query($sql);

	// Mysql_num_row is counting table row
	$count=mysql_num_rows($result);

	// fetch user role
	$role=mysql_fetch_array($result);

	// If result matched $myusername and $mypassword, table row must be 1 row
	if($count==1){
		// Register $myusername, $mypassword and redirect to file "login_success.php"
		$_SESSION['username'] = $usr;
		$_SESSION['role'] = $role[0];
		$_SESSION['errors'] = false;
		header("location:main.php");
	} else {
		$_SESSION['errors'] = array("Wrong Username or Password");
		header("location:index.php");
	}

?>