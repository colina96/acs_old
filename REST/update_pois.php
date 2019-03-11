<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {

	echo "got data ";
//	$poi_id = $_POST['poi_id'];

// DATA to find the record to update
	$poi_id = $_POST['poi_id'];
	//$mic_id = $_POST['mic_id'];

// DATA to update with 	
	$new_pt = $_POST['prep_type'];


	$sql = "update PURCHASE_ORDER_ITEMS set prep_type = ".$new_pt." 
		where id = ".$poi_id;
	
//>> TODO  may need to add date range 

	test_mysql_query($sql);
	echo $sql;
}
else {
	echo "not logged in";
}




