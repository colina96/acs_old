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
	$num_completed = $d['num_completed'];
	$sql = "update PLATING_ITEM set time_completed = now(),M2_temp=".$M2_temp.",num_completed=".$num_completed." where id = ".$id;
	test_mysql_query($sql);
	echo $sql;
	// update shift qty
	$shift = get_shift();
	$s = 's'.$shift.'_done';
	$sql = "update SHIFT_ORDERS set ".$s." = ".$s." + ".$num_completed." where menu_item_id=".$d['menu_item_id'];
	test_mysql_query($sql);
	echo $sql;
}
else {
	echo "not logged in";
}
