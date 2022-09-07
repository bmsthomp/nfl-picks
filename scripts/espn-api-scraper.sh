#!/usr/bin/env bash

tmpfile=$(mktemp /tmp/nfl-schedule.2021.XXXXXXXXXX)

echo "[" > $tmpfile

for ((week=1;week<=18;week++)); do
  echo "Getting week ${week}"

  http "http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?seasontype=2&week=${week}" | \
    jq '[.events[] | .competitions[] |
    	{
    		id: .id,
    		home: .competitors[0].team.abbreviation,
    		away: .competitors[1].team.abbreviation,
    		day: (if .status.type.detail | contains("TBD") then "TBD" else
    			.status.type.detail | split(",")[0] end),
    		time: (if .status.type.detail | contains("TBD") then "TBD" else
    			.status.type.detail | split("at ")[1] | split(" ")[0] end)
    	}
    ]' \
    >> $tmpfile

  [[ "$week" -lt 18 ]] && echo "," >> $tmpfile
done

echo "]" >> $tmpfile

python3 -m json.tool $tmpfile > nfl-schedule.2021.json

echo "Finished retrieving shedule"