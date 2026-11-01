<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>NFL Picks</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top site-navbar" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <?php if (isset($_SESSION['username'])) { ?>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#site-navigation" aria-controls="site-navigation" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <?php } ?>
        <a class="navbar-brand" href="<?php echo isset($_SESSION['username']) ? 'main.php' : 'index.php'; ?>">NFL <span>Picks</span></a>
      </div>
      <?php if (isset($_SESSION['username'])) { ?>
        <div id="site-navigation" class="navbar-collapse collapse navbar-responsive-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="main.php">Home</a></li>
            <li><a href="dash.php">Picks</a></li>
            <li><a href="leaderboard.php">Leaderboard</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      <?php } ?>
    </div>
  </nav>
  <?php 
  // require 'scoreParse.php'; 
  ?>

