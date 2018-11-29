<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_d.php";
$item = json_decode($_POST["data"],true);


// $shelf_life_days = $d['shelf_life_days'];
//$menu_item_id = $d['menu_item_id'];
//$shelf_life_days = 3; // TODO - where does this come from?????

$userID = $_SESSION['userID'];

	$plating_team_id = $item['plating_team_id'];
	$plating_item_id = $item['plating_item_id'];
	$component_id = $item['component_id'];
	$menu_item_component_id = $item['menu_item_component_id'];
	$M1_temp = $item['M1_temp'];
	
	$sql = "insert into PLATING_ITEM_COMPONENT ";
	$sql .= "(id, user_id, plating_item_id, menu_item_component_id, component_id,M1_time,M1_temp) ";
	$sql .= "values (null,".$userID.",".$plating_item_id.",".$menu_item_component_id.",".$component_id.",now(),".$M1_temp.")";
	test_mysql_query($sql);
	$response = Array();
	$response['id'] = mysql_insert_id();
	$response['component_id'] = $component_id;
	
	$result = mysql_query("select now() as M1_time");
	if ($result) {
		while($row = mysql_fetch_array($result)) {
			$response['M1_time'] = $row['M1_time'];
		}
	}

	$json = json_encode($response);
	echo $json;

 
?>