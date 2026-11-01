<?php
	require 'security.php';
	require_auth();

	require 'connection.php';
	require 'weekCalculator.php';

	$seasonResult = $con->query('SELECT DISTINCT season FROM schedule ORDER BY season DESC');
	$seasons = array();
	while ($seasonRow = $seasonResult->fetch_assoc()) {
		$seasons[] = (int) $seasonRow['season'];
	}

	$requestedSeason = filter_input(INPUT_GET, 'season', FILTER_VALIDATE_INT);
	if ($requestedSeason !== false && $requestedSeason !== null && in_array($requestedSeason, $seasons, true)) {
		$season = $requestedSeason;
	} elseif (in_array((int) $year, $seasons, true)) {
		$season = (int) $year;
	} else {
		$season = $seasons[0] ?? (int) $year;
	}

	$weekStatement = $con->prepare('SELECT DISTINCT week FROM schedule WHERE season = ? AND week <> 22 ORDER BY week DESC');
	$weekStatement->bind_param('i', $season);
	$weekStatement->execute();
	$weekResult = $weekStatement->get_result();
	$weeks = array();
	while ($weekRow = $weekResult->fetch_assoc()) {
		$weeks[] = (int) $weekRow['week'];
	}
	$weekStatement->close();

	$requestedWeek = filter_input(INPUT_GET, 'week', FILTER_VALIDATE_INT);
	if ($requestedWeek !== false && $requestedWeek !== null && in_array($requestedWeek, $weeks, true)) {
		$sweek = $requestedWeek;
	} elseif (in_array((int) $days, $weeks, true)) {
		$sweek = (int) $days;
	} else {
		$sweek = $weeks[0] ?? (int) $days;
	}

	$teamDataContents = file_get_contents(__DIR__ . '/data/team-data.json');
	if ($teamDataContents === false) {
		throw new RuntimeException('Unable to read NFL team data.');
	}
	$teamData = json_decode($teamDataContents, true, 512, JSON_THROW_ON_ERROR);

	$usersResult = $con->query('SELECT uid FROM users WHERE active = 1 ORDER BY uid');
	$uids = array();
	$totals = array();
	while ($userRow = $usersResult->fetch_assoc()) {
		$uid = $userRow['uid'];
		$uids[] = $uid;
		$totals[$uid] = 0;
	}

	$gameStatement = $con->prepare('SELECT geid, away, ascore, home, hscore, winner FROM schedule WHERE week = ? AND season = ? ORDER BY matchup');
	$gameStatement->bind_param('ii', $sweek, $season);
	$gameStatement->execute();
	$gameResult = $gameStatement->get_result();
	$games = array();
	while ($game = $gameResult->fetch_assoc()) {
		$games[] = $game;
	}
	$gameStatement->close();

	$pickStatement = $con->prepare('
		SELECT p.geid, p.uid, p.team
		FROM picks AS p
		INNER JOIN users AS u ON u.uid = p.uid AND u.active = 1
		WHERE p.season = ? AND p.week = ?');
	$pickStatement->bind_param('ii', $season, $sweek);
	$pickStatement->execute();
	$pickResult = $pickStatement->get_result();
	$picksByGame = array();
	while ($pick = $pickResult->fetch_assoc()) {
		$picksByGame[$pick['geid']][$pick['uid']] = $pick['team'];
	}
	$completedGames = 0;
	foreach ($games as $game) {
		if (!empty($game['winner'])) {
			$completedGames++;
			foreach ($uids as $uid) {
				if (($picksByGame[$game['geid']][$uid] ?? null) === $game['winner']) {
					$totals[$uid]++;
				}
			}
		}
	}
	$pickStatement->close();

	$leaders = array();
	if ($totals && $completedGames > 0) {
		$mostCorrect = max($totals);
		foreach ($totals as $uid => $total) {
			if ($total === $mostCorrect) {
				$leaders[$uid] = true;
			}
		}
	}

	require 'head.php';
?>

<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>Dashboard</h1>
			<p>Weekly results and picks</p>
		</div>
	</div>
</div>

<div class="container">
	<div class="col-lg-12">
		<div class="panel panel-default dashboard-controls">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6">
						<label for="week_select">Week</label>
						<select id="week_select" name="week" class="form-control">
							<?php foreach ($weeks as $week) { ?>
								<option value="<?php echo $week; ?>"<?php echo $week === $sweek ? ' selected' : ''; ?>>Week <?php echo $week; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-6">
						<label for="season_select">Season</label>
						<select id="season_select" name="season" class="form-control">
							<?php foreach ($seasons as $seasonOption) { ?>
								<option value="<?php echo $seasonOption; ?>"<?php echo $seasonOption === $season ? ' selected' : ''; ?>><?php echo $seasonOption; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dashboard-table">
				<thead>
					<tr>
						<th scope="col">Away</th>
						<th scope="col" class="dashboard-score">Score</th>
						<th scope="col">Home</th>
						<th scope="col" class="dashboard-score">Score</th>
						<?php foreach ($uids as $uid) { ?>
							<th scope="col" class="dashboard-player<?php echo isset($leaders[$uid]) ? ' dashboard-leader' : ''; ?>">
								<?php echo htmlspecialchars((string) $uid, ENT_QUOTES, 'UTF-8'); ?>
								<?php if (isset($leaders[$uid])) { ?><span class="dashboard-leader-label">Leader</span><?php } ?>
							</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php if ($games) { foreach ($games as $game) {
						$awayAbbreviation = (string) $game['away'];
						$homeAbbreviation = (string) $game['home'];
						$awayName = $teamData[$awayAbbreviation]['name'] ?? $awayAbbreviation;
						$homeName = $teamData[$homeAbbreviation]['name'] ?? $homeAbbreviation;
						$hasFinalScore = $game['winner'] !== null && $game['winner'] !== '';
						$awayScore = $hasFinalScore ? (string) $game['ascore'] : '-';
						$homeScore = $hasFinalScore ? (string) $game['hscore'] : '-';
					?>
						<tr>
							<td class="dashboard-team">
								<img class="teamLogo" src="./img/<?php echo htmlspecialchars(strtolower($awayAbbreviation), ENT_QUOTES, 'UTF-8'); ?>-logo.png" alt="">
								<?php echo htmlspecialchars($awayName, ENT_QUOTES, 'UTF-8'); ?>
							</td>
							<td class="dashboard-score"><?php echo htmlspecialchars($awayScore, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="dashboard-team">
								<img class="teamLogo" src="./img/<?php echo htmlspecialchars(strtolower($homeAbbreviation), ENT_QUOTES, 'UTF-8'); ?>-logo.png" alt="">
								<?php echo htmlspecialchars($homeName, ENT_QUOTES, 'UTF-8'); ?>
							</td>
							<td class="dashboard-score"><?php echo htmlspecialchars($homeScore, ENT_QUOTES, 'UTF-8'); ?></td>
							<?php foreach ($uids as $uid) {
								$pickTeam = $picksByGame[$game['geid']][$uid] ?? null;
								$isCorrect = $hasFinalScore && $pickTeam !== null && $pickTeam === $game['winner'];
								$cellClasses = array();
								if ($isCorrect) {
									$cellClasses[] = 'success';
								}
								if (isset($leaders[$uid])) {
									$cellClasses[] = 'dashboard-leader';
								}
							?>
								<td class="dashboard-pick <?php echo implode(' ', $cellClasses); ?>">
									<?php if ($pickTeam !== null) {
										$pickName = $teamData[$pickTeam]['name'] ?? $pickTeam;
									?>
										<img class="teamLogo" src="./img/<?php echo htmlspecialchars(strtolower($pickTeam), ENT_QUOTES, 'UTF-8'); ?>-logo.png" alt="<?php echo htmlspecialchars($pickName, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($pickName, ENT_QUOTES, 'UTF-8'); ?>">
									<?php } else { ?>
										<span class="text-muted">-</span>
									<?php } ?>
								</td>
							<?php } ?>
						</tr>
					<?php } ?>
					<tr class="dashboard-totals">
						<th scope="row" colspan="4">Correct picks</th>
						<?php foreach ($uids as $uid) { ?>
							<td class="dashboard-pick<?php echo isset($leaders[$uid]) ? ' dashboard-leader' : ''; ?>">
								<strong><?php echo (int) $totals[$uid]; ?></strong>
							</td>
						<?php } ?>
					</tr>
					<?php } else { ?>
						<tr>
							<td colspan="<?php echo 4 + count($uids); ?>" class="text-center text-muted">No games found for this week.</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	mysqli_close($con);
	require 'footer.php';
?>
