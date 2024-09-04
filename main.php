<?php 
	session_start();

	// if not authenticated go back to index.php
	if (!isset($_SESSION['username'])){
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
		} else {
			$error = false;
		}
	} else { 
		$edit = false;
	}

	require 'scoreParse.php';
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$user = $_SESSION['username'];
	$currentYearWeekMatchupsSQL="SELECT * FROM schedule WHERE week=$wkd and season=$year ORDER BY matchup ASC";
	$currentYearWeekMatchups=mysqli_query($con, $currentYearWeekMatchupsSQL);

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
				$matchup = 1;
				while($weekMatchups = mysqli_fetch_array($currentYearWeekMatchups)){

					# Grab pick for matchup $matchup
					$userPickSQL = "SELECT team FROM picks WHERE matchup=$matchup and week=$wkd and season=$year and uid='$user'";
					$userPick = mysqli_query($con, $userPickSQL);
					$pick = mysqli_fetch_array($userPick);

					# If the game has ended set a name on the row
					$gameState = "on";
					if (!empty($weekMatchups[8])) { $gameState = "end" ; }

					# Check pick against first $weekMatchups of $result (schedule) -> Set class="active"
					if (isset($pick[0])) {
						# $weekMatchups[5] = away team
						if ($pick[0] == $weekMatchups[5]){ 
							echo "<tr name=\"$gameState\" id=\"match$weekMatchups[1]\"><td class=\"clickableCell active\">$weekMatchups[5]</td><td class=\"clickableCell\">$weekMatchups[4]</td></tr>"; 
						}
						# $weekMatchups[4] = home team
						elseif ($pick[0] == $weekMatchups[4]) { 
							echo "<tr name=\"$gameState\" id=\"match$weekMatchups[1]\"><td class=\"clickableCell\">$weekMatchups[5]</td><td class=\"clickableCell active\">$weekMatchups[4]</td></tr>"; 
						}
					} else { 
						echo "<tr name=\"$gameState\" id=\"match$weekMatchups[1]\"><td class=\"clickableCell\">$weekMatchups[5]</td><td class=\"clickableCell\">$weekMatchups[4]</td></tr>"; 
					}
					$matchup ++;
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<div class="container">
	<div class="col-lg-12">
		<form action="updatePick.php" method="POST">
			<?php
				# Populate hidden table of picks
				for ($i = 1; $i <= $matchup; $i++){
					echo "<input name=\"match$i\"  style=\"display:none;\">";
				}
			?>
			<button name="submit" type="submit">Submit</button>

		</form>
	</div>
</div>
<?php require 'footer.php' ?>