<!DOCTYPE html>
<?php 
session_start(); 
?>
<?php include 'db.php' ?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Advanced Catering Services</title>

    <!-- Bootstrap -->
<link href="bootstrap.min.css" rel="stylesheet">
<script src="jquery.min.js"></script>
<script src="bootstrap.min.js"></script>
<!-- script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script -->

   <!-- Custom styles for this template -->
    <link href="navbar.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
 <nav class="navbar navbar-default">
      <div class="container">
  
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#codebrainery-toggle-nav" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>

        <div class="collapse navbar-collapse" id="codebrainery-toggle-nav">
          <ul class="nav navbar-nav">
            <li class='tabclass' onclick="openPage('USERS', this, 'red','tabcontent','tabclass')"><a onclick="openPage('USERS', this, 'red')" href="#">USERS</a></li>
            <li class='tabclass' onclick="openPage('MENU', this, 'red','tabcontent','tabclass')"><a href="#">MENU</a></li>
            <li class='tabclass' onclick="openPage('SETTINGS', this, 'red','tabcontent','tabclass')"><a href="#">SETTINGS</a></li>
            <li class='tabclass' onclick="openPage('REPORTS', this, 'red','tabcontent','tabclass')"><a href="#">REPORTS</a></li>
            <li class='tabclass' onclick="openPage('LABELS', this, 'red','tabcontent','tabclass')"><a href="#">LABELS SUPPLY</a></li>
            
            </ul>
            <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><?php if (!empty($_SESSION['userID'])) { echo $_SESSION['user']; } ?></a></li>
            </ul>
          
        </div>

      </div> <!-- close container div -->
</nav> <!-- close navbar nav -->
<div id='main_tabs' class='container'>
<div id="USERS" class="tabcontent">
  <?php include 'users.php' ?>
</div>

<div id="MENU" class="tabcontent">
  <?php include 'menu.php' ?>
</div>

<div id="SETTINGS" class="tabcontent">
  <h3>Settings</h3>
  <?php include 'acs_settings.php' ?>
</div>

<div id="REPORTS" class="tabcontent">
  <h3>About</h3>
  <p>Who we are and what we do.</p>
</div>
</div>
<div class="modal fade">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h2 class="form-signin-heading">Please sign in</h2>
        </div>
        <div class="container">

      		<form class="form-signin" method="post">
        
        	<label for="inputEmail" class="sr-only">Email address</label>
        	<input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
        	<label for="inputPassword" class="sr-only">Password</label>
        	<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        	<button class="btn btn-lg btn-primary btn-block" type="submit" name="login" value='1'>Sign in</button>
      		</form>

    	</div> <!-- /container -->
      </div>
    </div>
  </div>
<?php 
	if (empty($_SESSION['userID'])) {
?>
<script src="app.js"></script>
<?php } ?>
<script src="acs.js"></script>
