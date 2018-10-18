<?php
session_start();

include '../db.php';
////check_login();
// echo "login<br>";
if (!empty($_SESSION['userID'])) {
	$userID = $_SESSION['userID'];

//	echo $_POST['email'];
//	echo $_POST['password'];
	$ret = array();
	$ret['user_id'] = $userID;
	$ret['user'] = $_SESSION['user'];
	$ret['USER'] = $_SESSION['USER'];
	$json = json_encode($ret);
	echo $json;
}
else {
	$ret = array();
	$ret['user_id'] = -1;
	$json = json_encode($ret);
	echo $json;
}
