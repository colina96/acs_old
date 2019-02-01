<?php

session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	echo "got data ";
	$menu_item_id = $_POST['menu_item_id'];

	$name = $_POST['name'];
	$val = $_POST['val'];
	$sql = "update PARAMS set pvalue='".$val."' where pkey = '".$name."'";
	test_mysql_query($sql);
	echo $sql;

}
else {
	echo "not logged in";
}
