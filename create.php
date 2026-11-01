<?php
	require 'security.php';

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
		$_SESSION['errors'] = array("Could not create account.");
		header('Location: signup.php');
		exit;
	}

	$uid = trim((string) ($_POST['uid'] ?? ''));
	$fname = trim((string) ($_POST['fname'] ?? ''));
	$lname = trim((string) ($_POST['lname'] ?? ''));
	$password = (string) ($_POST['password'] ?? '');

	if ($uid === '' || $fname === '' || $lname === '' || $password === '') {
		header('Location: signup.php');
		exit;
	}

	require 'connection.php';

	$stmt = $con->prepare('SELECT uid FROM users WHERE uid = ? LIMIT 1');
	$stmt->bind_param('s', $uid);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = $result->num_rows;
	$stmt->close();

	if ($count > 0) {
		$_SESSION['errors'] = array("There is already an account associated with this username.");
		mysqli_close($con);
		header('Location: signup.php');
		exit;
	}

	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	$insert = $con->prepare('INSERT INTO users (uid, fname, lname, pw) VALUES (?, ?, ?, ?)');
	$insert->bind_param('ssss', $uid, $fname, $lname, $hashedPassword);
	$success = $insert->execute();
	$insert->close();
	mysqli_close($con);

	if (!$success) {
		$_SESSION['errors'] = array("Could not create account.");
		header('Location: signup.php');
		exit;
	}

	session_regenerate_id(true);
	$_SESSION['username'] = $uid;
	$_SESSION['role'] = 'user';
	header('Location: main.php');
	exit;
?>
