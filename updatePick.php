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
			$result = mysql_query($sql);

			if (mysql_num_rows($result) == 1){

				$geid = mysql_fetch_array($result);
				$psql = "SELECT team FROM $tbl_name WHERE week=$wkd and season=$year and geid='$geid[0]' and uid='$uid'";
				$result = mysql_query($psql);

				if (mysql_num_rows($result) == 1 && $result){
					//echo "trying an update";
					$result = mysql_query("UPDATE $tbl_name SET team='$pick' WHERE geid='$geid[0]' and uid='$uid'");
				} elseif ($result) {
					//echo "trying an insert";
					$result = mysql_query("INSERT INTO $tbl_name (uid, week, matchup, team, season, geid) VALUES ('$uid', $wkd, $geid[1], '$pick', $year, '$geid[0]')");
				}
				else {
					// query failed
					$_SESSION['errors'] = array("Error updating pick table.");
				}

			}
			else { $_SESSION['errors'] = array("Error updating picks. Pleae try again or contact the administrator."); unset($_SESSION['edit']); }
		}	
	}
	header("location:main.php");

?>