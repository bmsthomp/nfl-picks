<?php 
	session_start();

	// if not authenticated go back to index.php
	if (!$_SESSION['username']){
		session_destroy();
		header("location:index.php");
	}
	if (isset($_SESSION['edit'])) {
		$edit = true; 
		unset($_SESSION['edit']);
		if (isset($_SESSION['errors'])) {
			$error = true; 
			// testing
			unset($_SESSION['errors']);
		}
	}
	require 'scoreParse.php';
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$uid = $_SESSION['username'];
	$tbl_name = "schedule";
	$sql="SELECT * FROM $tbl_name WHERE week=$wkd and season=$year ORDER BY matchup ASC";
	$result=mysqli_query($con, $sql);
	$matches = 0;

	$uid = $_SESSION['username'];
	$tbl_name = "picks";

	require 'banner.php';
?> 

<div class="container">
	<div class="col-lg-12">
		<?php 
			if ($edit && $error) { echo "<div class=\"alert alert-warning\">There was an issue updating your picks.</div>"; }
			elseif ($edit && !$error) { echo "<div class=\"alert alert-success\">Picks successfully updated!</div>";}
			$error = false;
			$edit = false;

		?> 
		<table class="table">
			<thead>
				<th>Away</th>
				<th>Home</th>
			</thead>
			<tbody>
			<?php 
			#echo $matches[0];
				while($row = mysqli_fetch_array($result)){
					$matches ++;

					# Grab pick for matchup $matches
					$sql="SELECT team FROM $tbl_name WHERE matchup=$matches and week=$wkd and season=$year and uid='$uid'";
					$picks = mysqli_query($con, $sql);
					$pick = mysqli_fetch_array($picks);

					# If the game has ended set a name on the row
					$gameState = "on";
					if (!empty($row[8])) { $gameState = "end" ; }

					# Check pick against first row of $result (schedule) -> Set class="active"
					if ($pick[0] == $row[5]){ echo "<tr name=\"$gameState\" id=\"match$row[1]\"><td class=\"clickableCell active\">$row[5]</td><td class=\"clickableCell\">$row[4]</td></tr>"; }
					elseif ($pick[0] == $row[4]) { echo "<tr name=\"$gameState\" id=\"match$row[1]\"><td class=\"clickableCell\">$row[5]</td><td class=\"clickableCell active\">$row[4]</td></tr>"; }
					else { echo "<tr name=\"$gameState\" id=\"match$row[1]\"><td class=\"clickableCell\">$row[5]</td><td class=\"clickableCell\">$row[4]</td></tr>"; }
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<div class="container">
	<div class="col-lg-12">
		<form action="updatePick.php" method="POST">
			<?
				# Populate hidden table of picks
				for ($i = 1; $i <= $matches; $i++){
					echo "<input name=\"match$i\"  style=\"display:none;\">";
				}
			?>
			<button name="submit" type="submit">Submit</button>

		</form>
	</div>
</div>
<?php require 'footer.php' ?>