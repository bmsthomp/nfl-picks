<?php
	require 'security.php';
	require_auth();

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
		$_SESSION['edit'] = false;
		$_SESSION['errors'] = array("Invalid request.");
		header('Location: main.php');
		exit;
	}

	$_SESSION['edit'] = true;
	unset($_SESSION['errors']);
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "picks";
	$uid = $_SESSION['username'];
	$matchups = array();

	unset($_POST['submit']);
	foreach ($_POST as $key => $pick) {
		if ($key === 'csrf_token') { continue; }
		if ($pick != '') {
			$sql = "SELECT geid, matchup FROM schedule WHERE week=$wkd and season=$year and (home='" . mysqli_real_escape_string($con, $pick) . "' or away='" . mysqli_real_escape_string($con, $pick) . "')";
			$result = mysqli_query($con, $sql);

			if (mysqli_num_rows($result) == 1) {
				$geid = mysqli_fetch_array($result);
				$psql = "SELECT team FROM $tbl_name WHERE week=$wkd and season=$year and geid='" . mysqli_real_escape_string($con, $geid[0]) . "' and uid='" . mysqli_real_escape_string($con, $uid) . "'";
				$result = mysqli_query($con, $psql);

				if (mysqli_num_rows($result) == 1 && $result) {
					$result = mysqli_query($con, "UPDATE $tbl_name SET team='" . mysqli_real_escape_string($con, $pick) . "' WHERE geid='" . mysqli_real_escape_string($con, $geid[0]) . "' and uid='" . mysqli_real_escape_string($con, $uid) . "'");
				} elseif ($result) {
					$result = mysqli_query($con, "INSERT INTO $tbl_name (uid, week, matchup, team, season, geid) VALUES ('" . mysqli_real_escape_string($con, $uid) . "', $wkd, $geid[1], '" . mysqli_real_escape_string($con, $pick) . "', $year, '" . mysqli_real_escape_string($con, $geid[0]) . "')");
				} else {
					$_SESSION['errors'] = array("Error updating pick table.");
				}
			} else {
				$_SESSION['errors'] = array("Error updating picks. Pleae try again or contact the administrator.");
				unset($_SESSION['edit']);
			}
		}
	}
	mysqli_close($con);
	header("Location: main.php");
	exit;
?>
