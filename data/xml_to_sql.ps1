$file = "~\Downloads\20204.xml"

[xml]$xml = Get-Content $file

$xml.data.gms.g | %{
    $hscore = $_.hs
    $vscore = $_.vs
    $hteam = $_.h
    $vteam = $_.v
    $geid = $_.eid

    if ($hscore -gt $vscore) { $winner = $hteam } else { $winner = $vteam }
    if (-not $winner) { write-host "ERROR"}
    $string = "UPDATE schedule SET hscore='$hscore', ascore='$vscore', winner='$winner' WHERE geid='$geid';"

    write-host $string
}
# UPDATE schedule SET hscore='$home_score', ascore='$away_score', winner='$winner' WHERE geid='$geid'