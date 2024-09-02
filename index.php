<?php 
	session_start();
	require 'head.php';

	//check for failed log in
	if (isset($_SESSION['errors'])) {
		$error = true; 
	} else {
		$error = false;
	}

?>
<div class="jumbotron">
	<div class="container">
		<div class="col-lg-8">
			<h1>NFL Picks</h1>
		</div>
		<div class="col-lg-4">
			<!-- Login Form -->
			<?php if (!isset($_SESSION['username'])){ ?>

			<form class="form-horizontal" method="POST" action="login.php">
				<fieldset>
					<div class="form-group <?php if($error){ echo "has-error"; }?>">
						<div class="col-lg-12">
							<input class="form-control input-sm" name="username" required length="50" autofocus placeholder="Enter Username" value="<?php if(isset($_SESSION['username'])){ echo $_SESSION['username'];}?>">
						</div>
					</div>
					<div class="form-group <?php if($error){ echo "has-error"; }?>">
						<div class="col-lg-12">
							<input class="form-control input-sm" name="password" required length="50" type="password" placeholder="Enter Password">
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-2">
							<button name="submit" class="btn btn-default btn-sm" type="submit">Log In</button>
						</div>
						<div class="col-lg-10">
							<em>Not a member? <a href="signup.php">Sign up</a></em>
						</div>
					</div>
				</fieldset>
			</form>
			<?php
				} 
			?>
		</div>
	</div>
</div>


<?php require 'footer.php'; ?>