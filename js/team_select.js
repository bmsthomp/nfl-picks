$(document).ready(function(){

	// Highlight selected team for pick and remove previous active class
	// if it existed. 
	$(".clickableCell").click(function(){
		$parent = $(this).parent();
		$td = $(this);

    if ($parent.attr('name') != 'end'){
  		$($parent.children()).each(function(){
  			$(this).removeClass("active");
  		});

  		$td.addClass("active");

  		$match = $parent.attr('id');
  		$("[name="+$match+"]").val($(this).text());
    }

	});

	// reload admin page with season and week GET data
  	$("#week_select").change(function(){
  		$season = $("#season_select").find(":selected").val();
    	window.location='http://picks.bmsthompson.com/dash.php?week=' + this.value + '&season=' + $season;
  	});
  	$("#season_select").change(function(){
  		$week = $("#week_select").find(":selected").val();
    	window.location='http://picks.bmsthompson.com/dash.php?week=' + $week + '&season=' + this.value;
  	});
    $("#leaderboard_select").change(function(){
      $week = $("#leaderboard_select").find(":selected").val();
      window.location='http://picks.bmsthompson.com/leaderboard.php?season=' + this.value;
    });

  	// add plus button to last empty rows
  	// $("#adminTable").on('click', '.clickableGlyph', function(){
   //    $name = $("td .clickableGlyph").closest("tr").find("input")[0].name;
   //    $len = $name.length;
   //    $num = (parseInt($name.substring($len-1, $len)) +1).toString();
  	// 	$html = "<tr><td><input name=\"ateam" +$num+"\" length=\"5\"></td><td><input name=\"ascore" +$num+"\" length=\"5\"></td><td>@</td><td><input name=\"hteam" +$num+"\" length=\"5\"></td><td><input name=\"hscore"+$num+"\" length=\"5\"></td><td><span class=\"glyphicon glyphicon-plus-sign clickableGlyph \"></span></td></tr>";
  	// 	$('#adminTable').append($html);
  	// 	$(this).parent().empty();
  	// });


});
