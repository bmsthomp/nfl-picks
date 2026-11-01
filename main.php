<?php 
	require 'security.php';
	require_auth();

	require 'scoreParse.php';
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$user = $_SESSION['username'];
	$teamDataPath = __DIR__ . '/data/team-data.json';
	$teamDataContents = file_get_contents($teamDataPath);
	if ($teamDataContents === false) {
		throw new RuntimeException('Unable to read NFL team data.');
	}
	$teamData = json_decode($teamDataContents, true, 512, JSON_THROW_ON_ERROR);
	$recordSeason = (int) $year;
	$teamRecordsSQL = "
		SELECT team,
			SUM(outcome = 'W') AS wins,
			SUM(outcome = 'L') AS losses,
			SUM(outcome = 'T') AS ties
		FROM (
			SELECT home AS team,
				CASE WHEN winner = home THEN 'W' WHEN winner = 'T' THEN 'T' ELSE 'L' END AS outcome
			FROM schedule
			WHERE season = $recordSeason AND seasonType = 'REG' AND winner IS NOT NULL AND winner <> ''
			UNION ALL
			SELECT away AS team,
				CASE WHEN winner = away THEN 'W' WHEN winner = 'T' THEN 'T' ELSE 'L' END AS outcome
			FROM schedule
			WHERE season = $recordSeason AND seasonType = 'REG' AND winner IS NOT NULL AND winner <> ''
		) AS team_games
		GROUP BY team";
	$teamRecordsResult = mysqli_query($con, $teamRecordsSQL);
	$teamRecords = array();
	while ($record = mysqli_fetch_assoc($teamRecordsResult)) {
		$teamRecords[$record['team']] = (int) $record['wins'] . '-' . (int) $record['losses'];
		if ((int) $record['ties'] > 0) {
			$teamRecords[$record['team']] .= '-' . (int) $record['ties'];
		}
	}

	$currentYearWeekMatchupsSQL = "SELECT * FROM schedule WHERE week=$wkd and season=$year ORDER BY matchup ASC";
	$currentYearWeekMatchups = mysqli_query($con, $currentYearWeekMatchupsSQL);

	require 'banner.php';
?> 

<div class="container">
	<div class="col-lg-12">
		<?php 
			if (isset($_SESSION['edit']) && $_SESSION['edit']) {
				if (isset($_SESSION['errors'])) { echo '<div class="alert alert-warning">There was an issue updating your picks.</div>'; }
				else { echo '<div class="alert alert-success">Picks successfully updated!</div>'; }
				unset($_SESSION['edit']);
				unset($_SESSION['errors']);
			}
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
					$userPickSQL = "SELECT team FROM picks WHERE matchup=$matchup and week=$wkd and season=$year and uid='" . mysqli_real_escape_string($con, $user) . "'";
					$userPick = mysqli_query($con, $userPickSQL);
					$pick = mysqli_fetch_array($userPick);

					$awayTeamLowerCase = strtolower($weekMatchups[5]);
					$homeTeamLowerCase = strtolower($weekMatchups[4]);
					$awayTeamName = $teamData[$weekMatchups[5]]['name'] ?? (string) $weekMatchups[5];
					$homeTeamName = $teamData[$weekMatchups[4]]['name'] ?? (string) $weekMatchups[4];
					$awayRecord = $teamRecords[$weekMatchups[5]] ?? '0-0';
					$homeRecord = $teamRecords[$weekMatchups[4]] ?? '0-0';
					$awayTeam = '<img class="teamLogo" src="./img/' . e($awayTeamLowerCase) . '-logo.png" /> <span class="team-name sr-only">' . e((string) $weekMatchups[5]) . '</span><span class="team-full-name">' . e($awayTeamName) . '</span> <span class="team-record">' . e($awayRecord) . '</span>';
					$homeTeam = '<img class="teamLogo" src="./img/' . e($homeTeamLowerCase) . '-logo.png" /> <span class="team-name sr-only">' . e((string) $weekMatchups[4]) . '</span><span class="team-full-name">' . e($homeTeamName) . '</span> <span class="team-record">' . e($homeRecord) . '</span>';

					$gameState = "on";
					if (!empty($weekMatchups[8])) { $gameState = "end"; }

					if (isset($pick[0])) {
						if ($pick[0] == $weekMatchups[5]){
							echo '<tr name="' . $gameState . '" id="match' . $weekMatchups[1] . '"><td class="clickableCell active">' . $awayTeam . '</td><td class="clickableCell">' . $homeTeam . '</td></tr>';
						}
						elseif ($pick[0] == $weekMatchups[4]) {
							echo '<tr name="' . $gameState . '" id="match' . $weekMatchups[1] . '"><td class="clickableCell">' . $awayTeam . '</td><td class="clickableCell active">' . $homeTeam . '</td></tr>';
						}
					} else {
						echo '<tr name="' . $gameState . '" id="match' . $weekMatchups[1] . '"><td class="clickableCell">' . $awayTeam . '</td><td class="clickableCell">' . $homeTeam . '</td></tr>';
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
			<input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
			<?php
				for ($i = 1; $i <= $matchup; $i++){
					echo '<input name="match' . $i . '" style="display:none;">';
				}
			?>
			<button name="submit" type="submit">Submit</button>
		</form>
	</div>
</div>
<?php require 'footer.php' ?>
