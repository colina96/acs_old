<?php

session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$d = json_decode($_POST["data"],true);
	echo "got data ";
	$id = $d['id'];

		$sql = "update PLATING_ITEM set time_completed = now() where id = ".$id;
		test_mysql_query($sql);
		echo $sql;

}
else {
	echo "not logged in";
}
