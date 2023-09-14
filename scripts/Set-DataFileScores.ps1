
$week = 1

$endpoint = "http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?seasontype=2&week=$week"
$dataFile = "C:\Users\bmsth\git\nfl-picks\scripts\2023$week.xml"

$matchupWeek = $(Invoke-WebRequest $($endpoint + $object)).Content | ConvertFrom-Json
$dataFileContent = [xml] $(Get-Content $dataFile)

$matchupWeek.events | ForEach-Object {

	$competitors = $_.competitions[0].competitors

	$homeTeamObj = $competitors | Where-Object {$_.homeAway -eq 'home' }
	$awayTeamObj = $competitors | Where-Object {$_.homeAway -eq 'away' }

	$dataFileMatchupObj = $dataFileContent.data.gms.g | Where-Object { $_.h -eq $homeTeamObj.team.abbreviation }

	$dataFileMatchupObj.vs = $awayTeamObj.score
	$dataFileMatchupObj.hs = $homeTeamObj.score
	$dataFileMatchupObj.q = 'F'
}

$dataFileContent.Save("C:\Users\bmsth\git\nfl-picks\scripts\2023$week-scored.xml")
