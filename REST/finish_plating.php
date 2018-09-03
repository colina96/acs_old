<?php

session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$d = json_decode($_POST["data"],true);
	
	echo "got data ";
	$id = $d['id'];
	$M2_temp = $d['M2_temp'];
		$sql = "update PLATING_ITEM set time_completed = now(),M2_temp=".$M2_temp." where id = ".$id;
		test_mysql_query($sql);
		echo $sql;

}
else {
	echo "not logged in";
}
