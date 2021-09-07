<?php 
	session_start();
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "schedule";
	$matchups = array(); 

	$i = 1;
	$j = 1;
	$ascore = 0;
	unset($_POST['submit']);
	foreach ( $_POST as $score ){
		if (($i % 2) == 1) {
			$local = 'ascore';
			$ascore = $score;
		} else {
			$local = 'hscore';
			if ($ascore > $score){
				$winner = 'away';
			} elseif ($ascore < $score) {
				$winner = 'home';
			} else { $winner = null; }
		}
		if ($winner){
			$sql = mysqli_query($con, "SELECT $winner FROM $tbl_name WHERE matchup=$j and week=$days and season=$year");
			while ($row = mysqli_fetch_array($sql)) { $team = $row[0];}
			$query = mysqli_query($con, "UPDATE $tbl_name SET $local=$score, winner='$team' WHERE matchup=$j and week=$days and season=$year");
			$winner = null;
		} else {
			$query =mysqli_query($con, "UPDATE $tbl_name SET $local=$score WHERE matchup=$j and week=$days and season=$year");
		}
		if ($i % 2 == 0) { $j++; }
		$i++;
	
	}
	mysqli_close($con);
	header("location:winners.php");

?>