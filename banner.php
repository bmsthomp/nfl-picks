<div class="jumbotron">
	<div class="container">
		<div class="col-lg-12">
			<h1>
				<?php 
					if ($wkd == 18) {echo 'Wildcard Round'; }
					elseif ($wkd == 19) { echo 'Divisional Round'; }
					elseif ($wkd == 20 || $wkd == 21) { echo 'Conference Championships'; }
					elseif ($wkd >= 22) { echo 'Super Bowl'; }
					else { echo 'Week '. $wkd; } 
				?>
			</h1>
		</div>
	</div>
</div>