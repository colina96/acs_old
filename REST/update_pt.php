<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	echo "got data ";
	$code = $_POST['code'];
	$fld = $_POST['fld'];
	$value = $_POST['value'];
	if ($value == '') {
		$sql = "update PREP_TYPES set ".$fld." = null where code = '".$code."'";
	}
	else {
		$sql = "update PREP_TYPES set ".$fld." = ".$value." where code = '".$code."'";
	}
	test_mysql_query($sql);
echo $sql;
	
}
else {
	echo "not logged in";
}
