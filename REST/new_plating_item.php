<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_d.php";
$d = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got d: " . sizeof($d));
 

$plating_team = $d['plating_team'];
// $shelf_life_days = $d['shelf_life_days'];
$menu_item_id = $d['menu_item_id'];


$userID = $_SESSION['userID'];

	$sql = "insert into PLATING_ITEM ";
	$sql .= "(id, user_id, team_id, menu_item_id,time_started) ";
	$sql .= "values (null,".$userID.",".$plating_team.",".$menu_item_id.",now())";

	test_mysql_query($sql);
	$plating_item_id = mysql_insert_id();
	$response = array();
	$response['id'] = $plating_item_id;	
	$items = Array();
	// now create component entries
	foreach ($d['items'] as $item) {
		$component_id = $item['component_id'];
		$M1_temp = $item['M1_temp'];
		$sql = "insert into PLATING_ITEM_COMPONENT ";
		$sql .= "(id, user_id, plating_item_id, component_id,M1_time,M1_temp) ";
		$sql .= "values (null,".$userID.",".$plating_item_id.",".$component_id.",now(),".$M1_temp.")";
		test_mysql_query($sql);
		$comp = Array();
		$comp['id'] = mysql_insert_id();
		$comp['component_id'] = $component_id;
		$items[] = $comp;
	}
	$response['items'] = $items;

	
$json = json_encode($response);
echo $json;
// echo $sql."\n\n";
// 
 
?>