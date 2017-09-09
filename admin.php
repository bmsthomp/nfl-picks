<?php 
	session_start();
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "schedule";
	$sql="SELECT MIN(season) FROM $tbl_name";
	$result=mysql_query($sql);
	$oldest=mysql_fetch_array($result);

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
			<select id="week_select" name="week" class="form-control">
				<?php
					for ($i=21; $i > 0; $i--) { 
						if ($_GET["week"]){
							if ($i == $_GET["week"]) { echo "<option selected=\"selected\" value=\"$i\">Week $i</option>"; }		
							else { echo "<option value=\"$i\">Week $i</option>"; }
							$week = $_GET["week"];
						} else {
							if ($i == $days) { echo "<option selected=\"selected\" value=\"$i\">Week $i</option>"; }		
							else { echo "<option value=\"$i\">Week $i</option>"; }
							$week = $days;
						}
					}
				?>
			</select>
			</div>
			<div class="col-lg-6">
			<select id="season_select" name="season" class="form-control">
				<?php
					for ($i=$year; $i >= $oldest[0]; $i--) {
						if ($_GET["season"]){
							if ($i == $_GET["season"]) { echo "<option selected=\"selected\" value=\"$i\">$i</option>"; }		
							else { echo "<option value=\"$i\">$i</option>"; }
							$season = $_GET["season"];
						} else {
							if ($i == $year) { echo "<option selected=\"selected\" value=\"$i\">$i</option>"; }		
							else { echo "<option value=\"$i\">$i</option>"; }
							$season = $year;
						}
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
					$sql="SELECT home,away,hscore,ascore,matchup FROM $tbl_name WHERE week=$week and season=$season";
					$result=mysql_query($sql);
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_array($result)){
							echo "<tr><td>$row[1]</td><td><input name=\"ascore$row[4]\" value=\"$row[3]\" length=\"5\"></td><td>@</td><td>$row[0]</td><td><input name=\"hscore$row[4]\" value=\"$row[2]\" length=\"5\"></td></tr>";
						}
						$num = (int)$row[4] + 1;
						echo "<tr><td><input name=\"ateam$num\" length=\"5\"></td><td><input name=\"ascore$num\" length=\"5\"></td><td>@</td><td><input name=\"hteam$num\" length=\"5\"></td><td><input name=\"hscore$num\" length=\"5\"></td><td><span class=\"glyphicon glyphicon-plus-sign clickableGlyph \"></span></td></tr>";
					}
					else {
						echo "<tr><td><input name=\"ateam0\" length=\"5\"></td><td><input name=\"ascore0\" length=\"5\"></td><td>@</td><td><input name=\"hteam0\" length=\"5\"></td><td><input name=\"hscore0\" length=\"5\"></td><td><span class=\"glyphicon glyphicon-plus-sign clickableGlyph \"></span></td></tr>";
					}
				?>
			</table>
			<button name="submit" type="submit">Submit</button>
		</form>
	</div>
</div>

<?php require 'footer.php'; ?>