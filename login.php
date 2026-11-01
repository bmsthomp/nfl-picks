<?php
	require 'security.php';
	require 'connection.php';

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
		$_SESSION['errors'] = array("Wrong Username or Password");
		header('Location: index.php');
		exit;
	}

	$myusername = trim((string) ($_POST['username'] ?? ''));
	$mypassword = (string) ($_POST['password'] ?? '');

	if ($myusername === '' || $mypassword === '') {
		$_SESSION['errors'] = array("Wrong Username or Password");
		header('Location: index.php');
		exit;
	}

	$stmt = $con->prepare("SELECT uid, pw, role FROM users WHERE uid = ? LIMIT 1");
	$stmt->bind_param('s', $myusername);
	$stmt->execute();
	$result = $stmt->get_result();
	$user = $result->fetch_assoc();
	$stmt->close();
	mysqli_close($con);

	if ($user && verify_password($mypassword, (string) $user['pw'])) {
		session_regenerate_id(true);
		$_SESSION['username'] = $user['uid'];
		$_SESSION['role'] = $user['role'] ?? 'user';
		$_SESSION['errors'] = false;
		header('Location: main.php');
		exit;
	}

	$_SESSION['errors'] = array("Wrong Username or Password");
	header('Location: index.php');
	exit;
?>
