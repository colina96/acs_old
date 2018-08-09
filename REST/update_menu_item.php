<?php

session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	echo "got data ";
	$id = $_POST['id'];

		$plating_team = $_POST['plating_team'];
		$sql = "update MENU_ITEMS set plating_team = ".$plating_team." where id = ".$id;
		test_mysql_query($sql);
		echo $sql;

}
else {
	echo "not logged in";
}
