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
$num_labels = $d['description_labels'];
$num_trolley_labels = $d['trolley_labels'];
// $shelf_life_days = $d['shelf_life_days'];
$menu_item_id = $d['menu_item_id'];
$shelf_life_days = 3; // TODO - where does this come from?????

$userID = $_SESSION['userID'];

	$sql = "insert into PLATING_ITEM ";
	$sql .= "(id, user_id, team_id, menu_item_id,time_started,expiry_date,num_labels,num_trolley_labels) ";
	$sql .= "values (null,".$userID.",".$plating_team.",".$menu_item_id.",now(),DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY),".$num_labels.",".$num_trolley_labels.")";

	test_mysql_query($sql);
	$plating_item_id = mysql_insert_id();
	$response = array();
	$response['id'] = $plating_item_id;	
	$items = Array();
	// now create component entries
	foreach ($d['items'] as $item) {
		$component_id = $item['component_id'];
		$M1_temp = $item['M1_temp'];
		$menu_item_component_id = $item['id'];
		$sql = "insert into PLATING_ITEM_COMPONENT ";
		$sql .= "(id, user_id, plating_item_id, menu_item_component_id, component_id,M1_time,M1_temp) ";
		$sql .= "values (null,".$userID.",".$plating_item_id.",".$menu_item_component_id.",".$component_id.",now(),".$M1_temp.")";
		test_mysql_query($sql);
		$comp = Array();
		$comp['id'] = mysql_insert_id();
		$comp['component_id'] = $component_id;
		$items[] = $comp;
	}
	$response['items'] = $items;
	$result = mysql_query("select now() as M1_time, DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY) as expiry_date");
	if ($result) {
		while($row = mysql_fetch_array($result)) {
			$response['M1_time'] = $row['M1_time'];
			$response['expiry_date'] = $row['expiry_date'];
		}
	}

	
$json = json_encode($response);
echo $json;
// echo $sql."\n\n";
// 
function getIntVal($d)
{
	return(!empty($d)?$d:0);
}
?>