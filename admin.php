<?php
	require 'security.php';
	require_auth('admin');
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "schedule";
	$sql = "SELECT MIN(season) FROM $tbl_name";
	$result = mysqli_query($con, $sql);
	$oldest = mysqli_fetch_array($result);

	$selectedWeek = filter_input(INPUT_GET, 'week', FILTER_VALIDATE_INT) ?: $days;
	$selectedSeason = filter_input(INPUT_GET, 'season', FILTER_VALIDATE_INT) ?: $year;
	$week = $selectedWeek;
	$season = $selectedSeason;
?>
<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>Admin Panel</h1>
		</div>
	</div>
</div>

<div class="container">
	<div class="col-lg-12">
		<div class="col-lg-6">
		<form action="updateSchedule.php" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
			<select id="week_select" name="week" class="form-control">
				<?php
					for ($i=21; $i > 0; $i--) {
						$selected = ($selectedWeek == $i) ? ' selected="selected"' : '';
						echo '<option' . $selected . ' value="' . $i . '">Week ' . $i . '</option>';
					}
				?>
			</select>
		</div>
		<div class="col-lg-6">
			<select id="season_select" name="season" class="form-control">
				<?php
					for ($i=$year; $i >= $oldest[0]; $i--) {
						$selected = ($selectedSeason == $i) ? ' selected="selected"' : '';
						echo '<option' . $selected . ' value="' . $i . '">' . $i . '</option>';
					}
				?>
			</select>
		</div>
		<table id="adminTable" class="table">
			<tr>
				<th>Away</th>
				<th>Away Score</th>
				<th></th>
				<th>Home</th>
				<th>Home Score</th>
				<th></th>
			</tr>
			<?php
				$sql = "SELECT home,away,hscore,ascore,matchup FROM $tbl_name WHERE week=$selectedWeek and season=$selectedSeason";
				$result = mysqli_query($con, $sql);
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_array($result)){
						echo '<tr><td>' . e((string) $row[1]) . '</td><td><input name="ascore' . $row[4] . '" value="' . e((string) $row[3]) . '" length="5"></td><td>@</td><td>' . e((string) $row[0]) . '</td><td><input name="hscore' . $row[4] . '" value="' . e((string) $row[2]) . '" length="5"></td></tr>';
					}
					$num = (int)$row[4] + 1;
					echo '<tr><td><input name="ateam' . $num . '" length="5"></td><td><input name="ascore' . $num . '" length="5"></td><td>@</td><td><input name="hteam' . $num . '" length="5"></td><td><input name="hscore' . $num . '" length="5"></td><td><span class="glyphicon glyphicon-plus-sign clickableGlyph "></span></td></tr>';
				} else {
					echo '<tr><td><input name="ateam0" length="5"></td><td><input name="ascore0" length="5"></td><td>@</td><td><input name="hteam0" length="5"></td><td><input name="hscore0" length="5"></td><td><span class="glyphicon glyphicon-plus-sign clickableGlyph "></span></td></tr>';
				}
			?>
		</table>
		<button name="submit" type="submit">Submit</button>
		</form>
	</div>
</div>

<?php
	mysqli_close($con);
	require 'footer.php';
?>
