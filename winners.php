<?php 
	session_start();
	require 'head.php';
	require 'connection.php';
	require 'weekCalculator.php';

	$tbl_name = "schedule";
	$sql="SELECT * FROM $tbl_name WHERE week=$days and season=$year";
	$result=mysql_query($sql);


?>

<div class="container">
	<div class="col-lg-12">
		<form class="form-horizontal" method="POST" action="winnersUpdate.php">
			<table class="table">
				<thead>
					<th>Away</th>
					<th>Score</th>
					<th>Home</th>
					<th>Score</th>
				</thead>
				<tbody>
				<?php 
				echo $matches[0];
					while($row = mysql_fetch_array($result)){
						$matches ++; 
						echo "<tr><td>$row[5]</td><td><input name=\"ascore$matches\" value=\"$row[7]\"></td><td>$row[4]</td><td><input name=\"hscore$matches\" value=\"$row[6]\"></td></tr>";
					}
				?>
				</tbody>
			</table>
			<button name="submit" type="submit">Submit</button>
		</form>
	</div>
</div>



<?php require 'footer.php'; ?>