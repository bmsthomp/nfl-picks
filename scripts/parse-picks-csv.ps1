<# 

#>
$data = Import-Csv $(Resolve-Path ..\data\pick_data_2022.csv)

$weeks = 1..22
$year = 2022

foreach ($week in $weeks) {

    $weekFile = $(New-Item -Type File "$year$week.xml")
    Add-Content -Path $weekFile -Value "<?xml version='1.0' encoding='utf-8'?><data><gms w=`"$week`" y=`"$year`">"
    $weekData = $($data | Where-Object { $_.Week -eq $week })

    foreach ($matchup in $weekData) {

        $dataString = "<g eid=`"$($matchup.GEID)`" v=`"$($matchup.Away)`" h=`"$($matchup.Home)`" vs=`"0`" hs=`"0`" q=`"`" />"
        Add-Content -Path $weekFile -Value $dataString
    }

    Add-Content -Path $weekFile -Value "</gms></data>"

}

