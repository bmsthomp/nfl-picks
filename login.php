<?php
	session_start();

	require 'connection.php';
	$tbl_name = "users";

	// username and password sent from form 
	$myusername=$_POST['username'];
	$mypassword=md5($_POST['password']);

	// To protect MySQL injection (more detail about MySQL injection)
	$usr = mysqli_real_escape_string($con, $myusername);
	$pwd = mysqli_real_escape_string($con, $mypassword);

	$sql="SELECT role FROM $tbl_name WHERE uid='$usr' and pw='$pwd'";

	$result = mysqli_query($con, $sql);
	$count = mysqli_num_rows($result);

	// fetch user role (unimplemented)
	// $role = mysqli_fetch_array($result);

	mysqli_free_result($result);
	mysqli_close($con);

	// If result matched $myusername and $mypassword, table row must be 1 row
	if($count==1){
		// Register $myusername, $mypassword and redirect to file "login_success.php"
		$_SESSION['username'] = $usr;
		$_SESSION['role'] = "user";
		$_SESSION['errors'] = false;

		header("location:main.php");
	} else {
		$_SESSION['errors'] = array("Wrong Username or Password");
		header("location:index.php");
	}

?>