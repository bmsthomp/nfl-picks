<?php 
	session_start();

	// if not authenticated go back to index.php
	if (!isset($_SESSION['username'])){
		session_destroy();
		header("location:index.php");
	}

	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';
?>

<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>Dashboard</h1>
		</div>
	</div>
</div>

<div class="container">
	<div class="col-lg-12">
		<div class="col-lg-6">
			<select id="week_select" name="week" class="form-control">
				<?php
					$maxWeekSQL = "SELECT MAX(week) FROM schedule WHERE season=" . (isset($_GET['season']) ? $_GET['season'] : $year);
					$maxweek = mysqli_fetch_array(mysqli_query($con, $maxWeekSQL));

					for ($i=$maxweek[0]; $i > 0; $i--) { 

						if ($i == 22) { $i --; } # No week 22 (pro bowl)

						if (isset($_GET["week"])){
							if ($i == $_GET["week"]) { 
								echo "<option selected=\"selected\" value=\"$i\">Week $i</option>"; 
							}
							else {
								echo "<option value=\"$i\">Week $i</option>";
							}
							$sweek = $_GET["week"];
						} else {
							if ($i == $days) { 
								echo "<option selected=\"selected\" value=\"$i\">Week $i</option>"; 
							}
							else { 
								echo "<option value=\"$i\">Week $i</option>"; 
							}
							$sweek = $wkd <= 23 ? $days : 23;
						}
					}
				?>
			</select>
		</div>
		<div class="col-lg-6">
			<select id="season_select" name="season" class="form-control">
				<?php
					for ($i=$year; $i >= 2015; $i--) {
						if (isset($_GET["season"])){
							if ($i == $_GET["season"]) {
								echo "<option selected=\"selected\" value=\"$i\">$i</option>"; 
							}
							else { 
								echo "<option value=\"$i\">$i</option>"; 
							}
							$season = $_GET["season"];
						} else {
							if ($i == $year) { 
								echo "<option selected=\"selected\" value=\"$i\">$i</option>";
							}
							else { 
								echo "<option value=\"$i\">$i</option>";
							}
							$season = $year;
						}
					}
				?>
			</select>
		</div>


<?php 
	// fetch uids

	$uids = array();

	$result = mysqli_query($con, "SELECT uid FROM users WHERE active = 1");
	while ($uid = mysqli_fetch_assoc($result)) { array_push($uids, $uid); }

	// fetch geids
	$geids = array();
	$sql = "SELECT geid, away, ascore, home, hscore, winner FROM schedule where week=$sweek and season=$season";
	$result = mysqli_query($con, $sql);
	while ($geid = mysqli_fetch_assoc($result)) { array_push($geids, $geid); }

	// fetch weekly totals
	$totals = array();
	$sql = "SELECT picks.uid, SUM(picks.team = schedule.winner) as picks FROM picks INNER JOIN schedule ON picks.week = schedule.week AND picks.season = schedule.season AND picks.matchup = schedule.matchup WHERE schedule.week = $sweek AND schedule.season = $season GROUP BY picks.uid";
	$result = mysqli_query($con, $sql);

?>

		<table class="table">
			<thead>
				<th>Away</th>
				<th></th>
				<th>Home</th>
				<th></th>
				<th></th>
				<?php foreach($uids as $uid) { echo "<th>{$uid['uid']}</th>"; } ?>
			</thead>
			<tbody>
				<?php
					foreach ($geids as $geid) {
						$winner = $geid['winner'];
						echo "<tr><td class=\"col-sm-1\">{$geid['away']}</td><td class=\"col-sm-1\">{$geid['ascore']}</td><td class=\"col-sm-1\">{$geid['home']}</td><td class=\"col-sm-1\">{$geid['hscore']}</td><td class=\"col-sm-1\"></td>";
						foreach ($uids as $uid){
							$sql = "SELECT team FROM picks WHERE uid='{$uid['uid']}' and geid='{$geid['geid']}'";
							$r = mysqli_query($con, $sql);

							if(mysqli_num_rows($r) == 1){
								$team = mysqli_fetch_assoc($r);
								if ($team['team'] == $winner){ 
									echo "<td class=\"col-sm-1 success\">{$team['team']}</td>"; 
								} else { 
									echo "<td class=\"col-sm-1\">{$team['team']}</td>"; 
								}
								
							} else {
								echo "<td> - </td>";
							}
						}
						echo "</tr>";
					}
					mysqli_close($con);
				?>
				<tr>
					<td class="col-sm-1">TOTALS</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<?php
						if (mysqli_num_rows($result) == 0){
							for ($i=0; $i < count($uids); $i++) { 
								echo "<td>0</td>";
							}
						}
						else {
							while($total = mysqli_fetch_array($result)){
								echo "<td>$total[1]</td>";
							}
						}
					?>
				</tr>
			</tbody>
	</div>
</div>


<?php require 'footer.php'; ?>