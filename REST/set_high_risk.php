<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
$comp = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got comp: " . sizeof($comp));
 

$menu_id = $comp['menu_id'];
$menu_item_component_id = $comp['menu_item_component_id'];
$high_risk = $comp['high_risk'];


$userID = $_SESSION['userID'];

	$sql = "update MENU_ITEM_COMPONENTS set high_risk=".$high_risk." where id=".$menu_item_component_id;
	

test_mysql_query($sql);
echo $sql."\n\n";
// 
 
?>