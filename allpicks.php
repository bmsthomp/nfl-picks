<?php 
	session_start();

	// if not authenticated go back to index.php
	if (!$_SESSION['username']){
		session_destroy();
		header("location:index.php");
	}

	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';
	require 'banner.php';
?>



<?php

	$uid = $_SESSION['username'];
	$tbl_name = "picks";
	$tbl_name_2 = "schedule";
	$sql="SELECT schedule.away, schedule.ascore, schedule.home, schedule.hscore, schedule.matchup, picks.uid, picks.team FROM schedule INNER JOIN picks ON picks.week = schedule.week AND picks.season = schedule.season AND picks.matchup = schedule.matchup WHERE schedule.season = $year AND schedule.week = $days ORDER BY picks.uid";
	$result=mysqli_query($con, $sql);
	$resultSet = array();
	while ($row = mysqli_fetch_array($result)) {
		array_push($resultSet, array("uid" => $row['uid'], "team" => $row['team']));
	}

	$uid = false; 
	$pArray = array();
	$i = 0;
	$j = 0;
	$len = count($resultSet);
	$uids = array();
	foreach ($resultSet as $result){
		if ($uid == $result['uid'] && $j != $len-1){
			array_push($teams, $result['team']);
		} elseif ($j == $len-1){
			array_push($teams, $result['team']);
			$pArray[$uid] = $teams;
			array_push($uids, $uid);
		} elseif ($i != 0) {
			$pArray[$uid] = $teams;
			array_push($uids, $uid);
			$uid = $result['uid'];
			
			unset($teams);
			$teams = array();
			array_push($teams, $result['uid'], $result['team']);
			$i = 0;
		} else {
			$uid = $result['uid'];
			$teams = array();
			array_push($teams, $result['uid'], $result['team']);
		}
		$i++; $j++;
	}

	$sql = "SELECT matchup, away, home, ascore, hscore FROM $tbl_name_2 WHERE season=$year and week=$days";
	$sResult=mysqli_query($con, $sql);
	#print_r($resultSet);
	mysqli_close($con);
?> 



		<table class="table">
			<thead>
				<th>Away</th>
				<th></th>
				<th>Home</th>
				<th></th>
				<?php foreach ($uids as $uid){ echo "<th>$uid</th>"; } ?>
			</thead>

			<tbody>
				<?php 
					$i = 1;
					$j = count($uids);
					while ($row = mysqli_fetch_array($sResult)){
						echo "<tr><td>$row[1]</td><td>$row[3]</td><td>$row[2]</td><td>$row[4]</td>";
						foreach ($uids as $uid) {
							echo "<td>{$pArray[$uid][$i]}</td>";
						}
						echo "</tr>";
						$i++;
					}
				?>
				
			</tbody>
		</table>
	</div>
</div>

<?php require 'footer.php' ?>