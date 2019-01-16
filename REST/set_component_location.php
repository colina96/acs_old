<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
$comp = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got comp: " . sizeof($comp));
 

$id = $comp['id'];

$location = $comp['location'];


$userID = $_SESSION['userID'];

	$sql = "update MENU_ITEM_COMPONENTS set location='".$location."' where id=".$id;
	

test_mysql_query($sql);
echo $sql."\n\n";
// 
 
?>