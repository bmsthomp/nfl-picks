<?php
	session_start(); 
	require 'head.php';?>

<div class="container">
	<div class="col-lg-12">
		<div class="page-header"><div class="row"><div class="col-lg-12"><h1>Create Account</h1></div></div></div>

		<?php 
			if (isset($_SESSION['errors'])){
				foreach($_SESSION['errors'] as $error){
					echo $error;
				}
			}
		?>
		<form method='POST' class="form-horizontal" action="create.php">
			<fieldset>
			<div class="form-group">
				<div class="col-lg-3"></div>
				<div class="col-lg-6">
					<input class="form-control input-sm" name='uid' length='125' type='text' required placeholder="Username">
				</div>
			<div class="col-lg-3"></div>
			</div>
			<div class="form-group">
				<div class="col-lg-3"></div>
				<div class="col-lg-6">
					<input class="form-control input-sm" name='fname' length='125' type='text' required placeholder="First Name">
				</div>
				<div class="col-lg-3"></div>
			</div>

			<div class="form-group">
				<div class="col-lg-3"></div>
				<div class="col-lg-6">
					<input class="form-control input-sm" name='lname' length='125' type='text' required placeholder="Last Name">
				</div>
				<div class="col-lg-3"></div>
			</div>
			
			<div class="form-group">
				<div class="col-lg-3"></div>
				<div class="col-lg-6">
					<input class="form-control input-sm" name='password' length='125' type='password' required placeholder="Password">
				</div>
				<div class="col-lg-3"></div>
			</div>
			
			<div class="col-lg-3"></div><input style="margin-left:7px;" type='submit' >
			</fieldset>
		</form>
	</div>
</div>



<?php 
	unset($_SESSION['errors']);
	require 'footer.php'; 
?>