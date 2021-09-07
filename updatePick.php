<?php 
	session_start();
	$_SESSION['edit'] = true;
	unset($_SESSION['errors']);
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "picks";
	$uid = $_SESSION['username'];
	$matchups = array(); 

	unset($_POST['submit']);
	//echo "before loop";
	foreach ( $_POST as $pick ){
		if ($pick != ''){
			//echo $pick . " is not null";
			$sql = "SELECT geid, matchup FROM schedule WHERE week=$wkd and season=$year and (home='$pick' or away='$pick')";
			$result = mysqli_query($con, $sql);

			if (mysqli_num_rows($result) == 1){

				$geid = mysqli_fetch_array($result);
				$psql = "SELECT team FROM $tbl_name WHERE week=$wkd and season=$year and geid='$geid[0]' and uid='$uid'";
				$result = mysqli_query($con, $psql);

				if (mysqli_num_rows($result) == 1 && $result){
					//echo "trying an update";
					$result = mysqli_query($con, "UPDATE $tbl_name SET team='$pick' WHERE geid='$geid[0]' and uid='$uid'");
				} elseif ($result) {
					//echo "trying an insert";
					$result = mysqli_query($con, "INSERT INTO $tbl_name (uid, week, matchup, team, season, geid) VALUES ('$uid', $wkd, $geid[1], '$pick', $year, '$geid[0]')");
				}
				else {
					// query failed
					$_SESSION['errors'] = array("Error updating pick table.");
				}

			}
			else { $_SESSION['errors'] = array("Error updating picks. Pleae try again or contact the administrator."); unset($_SESSION['edit']); }
		}	
	}
	mysqli_close($con);
	header("location:main.php");

?>