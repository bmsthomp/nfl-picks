<?php

require('connection.php');
require('weekCalculator.php');

if (isset($_GET["week"]) && isset($_GET["season"]) && isset($_GET["seasonType"])){
    $days = $_GET["week"];
    $year = $_GET["season"];
    $seasonType = $_GET["seasonType"];
} 
//load source code, depending on the current week, of the website into a variable as a string
// $url = "http://www.nfl.com/ajax/scorestrip?season=$year&seasonType=$seasonType&week=$days";
$url = $dataRoot . $year . "/" . $year . $days . ".xml";

#$url = "http://www.nfl.com/liveupdate/scorestrip/ss.xml"; //LIVE GAMES

if ($xmlData = file_get_contents($url)) {
    $xml = simplexml_load_string($xmlData);
    $json = json_encode($xml);
    $games = json_decode($json, true);
}

foreach ($games['gms']['g'] as $gameArray) {

    # Must parse array differently if returning a single
    if ($gameArray['@attributes'] != null) { $game = $gameArray['@attributes']; }
    else { $game = $gameArray; }

    $geid = $game['eid'];

    $away_team = $game['v'];
    $home_team = $game['h'];
    $away_score = (int)$game['vs'];
    $home_score = (int)$game['hs'];

    $sql = "SELECT geid FROM schedule WHERE geid=$geid";
    $eid = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($eid) == 0) {

        $sql = "SELECT MAX(matchup) FROM schedule WHERE week=$days and season=$year";
        $results = mysqli_query($con, $sql);
        $mxMtchup = mysqli_fetch_array($results);

        if ($mxMtchup[0] != null && $mxMtchup[0] != '') { $matchup = $mxMtchup[0] +1; }
        else { $matchup = 1; }

        $sql = "INSERT INTO schedule (matchup, week, season, home, away, geid, seasonType) VALUES ('$matchup', $days, $year, '$home_team', '$away_team', '$geid', '$seasonType')";
        $test = mysqli_query($con, $sql);
        if (!$test){echo "failed";}

    } else {

        //ONLY PULL SCORES FROM COMPLETED GAMES - F=FINAL, FO=FINAL OVERTIME
        if ($game['q'] == 'F' || $game['q'] == 'FO') {

            if ($away_score == $home_score){
                $winner = "T";
            } else {
                $winner = ($away_score > $home_score) ? $away_team : $home_team;
            }
            $sql = "UPDATE schedule SET hscore='$home_score', ascore='$away_score', winner='$winner' WHERE geid='$geid'";
            mysqli_query($con, $sql);
            
        }
    }
}
mysqli_close($con);
