$file = "202111.xml"

[xml]$xml = Get-Content ..\scripts\$file

$xml.data.gms.g | ForEach-Object {
    $hscore = [int]$_.hs
    $vscore = [int]$_.vs
    $hteam = $_.h
    $vteam = $_.v
    $geid = $_.eid
    $winner = ""

    if ($hscore -gt $vscore) { $winner = $hteam } 
    elseif ($vscore -gt $hscore) { $winner = $vteam }
    else { $winner = "T" }

    if (-not $winner) { write-host "ERROR" }

    $string = "UPDATE schedule SET hscore='$hscore', ascore='$vscore', winner='$winner' WHERE geid='$geid';"

    write-host $string
}
# UPDATE schedule SET hscore='$home_score', ascore='$away_score', winner='$winner' WHERE geid='$geid'