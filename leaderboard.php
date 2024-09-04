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

	$sql = "SELECT picks.uid, SUM(picks.team = schedule.winner) as picks FROM picks INNER JOIN schedule ON picks.week = schedule.week AND picks.season = schedule.season AND picks.matchup = schedule.matchup WHERE picks.season=" . (isset($_GET['season']) ? $_GET['season'] : $year) . " GROUP BY picks.uid ORDER BY picks DESC";
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
				$minSeasonValueSQL = "SELECT MIN(season) FROM schedule";
				$minSeasonValue = mysqli_fetch_array(mysqli_query($con, $minSeasonValueSQL));
				mysqli_close($con);

				for ($i=$year; $i >= $minSeasonValue[0]; $i--) {
					$selectedSeason = isset($_GET["season"]) ? $_GET["season"] : $year;
					if ($i == $selectedSeason) { 
						echo "<option selected=\"selected\" value=\"$i\">$i</option>"; 
					}
					else { 
						echo "<option value=\"$i\">$i</option>"; 
					}
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