<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	echo "got data ";
	$comp_id = $_POST['id'];
	if (!empty($_POST['prep_type'])) {
		$prep_type = $_POST['prep_type'];
		$sql = "update MENU_ITEM_COMPONENTS set prep_type = ".$prep_type." where id = ".$comp_id;
	}
	
	if (!empty($_POST['probe_type'])) {
		$probe_type = $_POST['probe_type'];
		$sql = "update MENU_ITEM_COMPONENTS set probe_type = ".$probe_type." where id = ".$comp_id;
	}
	// echo $_POST['id'];
	//echo $_POST['prep_type'];
	//$sql = "update MENU_ITEM_COMPONENTS set prep_type = ".$prep_type." where id = ".$comp_id;
	test_mysql_query($sql);
echo $sql;
	
}
else {
	echo "not logged in";
}
