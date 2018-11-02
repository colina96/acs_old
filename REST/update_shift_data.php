<?php

session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	echo "got data ";
	$menu_item_id = $_POST['menu_item_id'];

	$shift = $_POST['shift_id'];
	$value = $_POST['val'];
	$sql = "update SHIFT_ORDERS set s".$shift." = ".$value." where menu_item_id = ".$menu_item_id;
	test_mysql_query($sql);
	echo $sql;

}
else {
	echo "not logged in";
}