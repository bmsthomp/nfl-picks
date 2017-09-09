<?php
	
	$today = new DateTime(date('Y-m-d'));
	# We want to calculate from the first preseason game but if we are in January
	# the season will need to be the previous year
	$year = $today->format('m') < 7 ? $today->format('Y') -1 : $today->format('Y');
	$stDate = "http://www.nfl.com/ajax/scorestrip?season=$year&seasonType=REG&week=1";

	if ($sxmlData = file_get_contents($stDate)) {
    	$sxml = simplexml_load_string($sxmlData);
    	$sjson = json_encode($sxml);
    	$sgames = json_decode($sjson, true);
	}   
		
	$sGame = $sgames['gms']['g'][0]['@attributes']['eid'];
	$start = new DateTime(substr($sGame, 0, -2));

	$week = date_diff($today, $start);
	$days = $week->format('%a');

	$days /= 7;
	$days += 1;

	$days = floor($days);
	if ($days == 21) $days = 22;
	$wkd = $days;

	# REG: 1-17
	# POST: 18-20, 22
	# Note -- week 21 is skipped before the super bowl
	$seasonType = $days > 17 ? "POST" : "REG";

	if (strtotime($today->format('Y-m-d')) < strtotime($start->format('Y-m-d'))) { $wkd = 1; }

?>
