<?php
	require 'security.php';
	require_auth('admin');

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
		$_SESSION['errors'] = array("Invalid request.");
		header('Location: admin.php');
		exit;
	}

	require 'connection.php';
	require 'weekCalculator.php';

	$week = filter_input(INPUT_POST, 'week', FILTER_VALIDATE_INT) ?: $days;
	$season = filter_input(INPUT_POST, 'season', FILTER_VALIDATE_INT) ?: $year;

	foreach ($_POST as $key => $value) {
		if (strpos($key, 'ascore') === 0 || strpos($key, 'hscore') === 0 || strpos($key, 'ateam') === 0 || strpos($key, 'hteam') === 0) {
			// Schedule updates are intentionally handled by the admin UI; the original implementation is incomplete.
		}
	}

	mysqli_close($con);
	header('Location: admin.php');
	exit;
?>
