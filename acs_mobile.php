
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
<!--  >link href="bootstrap.min.css" rel="stylesheet">
<script src="jquery.min.js"></script>
<script src="bootstrap.min.js"></script -->


   <!-- Custom styles for this template -->
    <link href="acs_mobile.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class='mobile_main'>
  <?php 
	if (empty($_SESSION['userID'])) {
?>
	<div class='login_head'>Login</div>
	<form method='GET'>
	<input class='input' name='email' placeholder='email'>
	<input name='password' type='password' placeholder='password'>
	<input type='submit' name='login' value='Login'>
	</form>
<?php 
	}
	else {
?>
		<div class='m_main'><?php if (!empty($_SESSION['userID'])) { echo $_SESSION['user']; } ?>
		
		</div>
	</div>
<?php 
	}
?>
  </div>