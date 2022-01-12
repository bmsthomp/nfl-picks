<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>
				<?php 
					if ($wkd == 19) {echo 'Wildcard Round'; }
					elseif ($wkd == 20) { echo 'Divisional Round'; }
					elseif ($wkd == 21 || $wkd == 22) { echo 'Conference Championships'; }
					elseif ($wkd >= 23) { echo 'Super Bowl'; }
					else { echo 'Week '. $wkd; } 
				?>
			</h1>
		</div>
	</div>
</div>