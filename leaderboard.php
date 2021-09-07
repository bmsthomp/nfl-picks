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

	$uid = $_SESSION['username'];
	$tbl_name = "picks";
	$tbl_name_2 = "schedule";
	$sql = "SELECT picks.uid, SUM(picks.team = schedule.winner) as picks FROM $tbl_name INNER JOIN $tbl_name_2 ON picks.week = schedule.week AND picks.season = schedule.season AND picks.matchup = schedule.matchup WHERE picks.season=" . (isset($_GET['season']) ? $_GET['season'] : $year) . " GROUP BY picks.uid ORDER BY picks DESC";
	$result = mysqli_query($con, $sql);
?> 

<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>Leaderboard</h1>
		</div>
	</div>
</div>

<div class="container">
	<div class="col-lg-12">
		<select id="leaderboard_select" name="week" class="form-control">
			<?php
				$ssql = "SELECT MIN(season) FROM schedule";
				$minyear = mysqli_fetch_array(mysqli_query($con, $ssql));
				mysqli_close($con);

				for ($i=$year; $i >= $minyear[0]; $i--) { 
					if ($i == $_GET["season"]) { echo "<option selected=\"selected\" value=\"$i\">$i</option>"; }
					else { echo "<option value=\"$i\">$i</option>"; }
				}
			?>
		</select>
	</div>
	<div class="col-lg-12">
		<table class="table">
			<thead>
				<th>User</th>
				<th>Score</th>
			</thead>
			<tbody>
				<?php
					while($row = mysqli_fetch_array($result)){
						echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php require 'footer.php' ?>