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
			$sql = mysql_query("SELECT $winner FROM $tbl_name WHERE matchup=$j and week=$days and season=$year");
			while ($row = mysql_fetch_array($sql)) { $team = $row[0];}
			$query = mysql_query("UPDATE $tbl_name SET $local=$score, winner='$team' WHERE matchup=$j and week=$days and season=$year");
			$winner = null;
		} else {
			$query = mysql_query("UPDATE $tbl_name SET $local=$score WHERE matchup=$j and week=$days and season=$year");
		}
		if ($i % 2 == 0) { $j++; }
		$i++;
	
	}

	header("location:winners.php");

?>