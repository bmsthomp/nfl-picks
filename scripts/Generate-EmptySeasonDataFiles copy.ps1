

$endpoint = "http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?seasontype=2&week="

foreach ($object in 1..18) {
	$matchupWeek = $(Invoke-WebRequest $($endpoint + $object)).Content | ConvertFrom-Json
	$matchupWeek.events | ForEach-Object { 
		Write-Host $($_.shortName.Replace("@", "") + " " + $_.week.number)
	}

}

$endpoint = "http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?seasontype=2&week=1"