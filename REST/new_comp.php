<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
// echo $_POST["data"];
$comp = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got comp: " . sizeof($comp));
 
$description = mysql_escape_string($comp['description']);
$prep_type = $comp['prep_type'];
$dock = empty($comp['dock']) ? null:$comp['dock'];
if ($prep_type == '') {
	$prep_type = 1;
}

$M1_chef_id = empty($comp['M1_chef_id']) ? 'null':$comp['M1_chef_id'];
$comp_id = $comp['comp_id'];
$shelf_life_days = empty($comp['shelf_life_days']) ? 0 : $comp['shelf_life_days'];
$M1_action_code = 'null';
$M1_action_code = empty($comp['M1_action_code']) ? 'null':$comp['M1_action_code'];
$M1_action_id = empty($comp['M1_action_id']) ? 'null':$comp['M1_action_id'];
$finished = empty($comp['finished']) ? 'null':'now()';
$M0 = false;	


$userID = $_SESSION['userID'];
if (!empty($comp['M1_temp'])) {
	$M1_temp = $comp['M1_temp'];
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id, started, M1_check_id, M1_temp, M1_time, M1_chef_id,M1_action_code,M1_action_id,shelf_life_days,expiry_date,finished) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.",".$M1_temp.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY),".$finished.")";
}
else if (!empty($comp['finished'])) {
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id, started, M1_check_id, M1_time, M1_chef_id,M1_action_code,M1_action_id,finished,shelf_life_days,expiry_date) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",now(),".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY))";
}
else { // can only be M0 - 
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id,started, M1_check_id) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.")";
	$M0 = true;
}
test_mysql_query($sql);
$ret = array();
$ret['id'] = mysql_insert_id();
$ret['description'] = $description;
$ret['comp_id'] = $comp_id;
$ret['dock'] = $dock;
$result = mysql_query("select now() as now,DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY) as expiry_date");
if ($result) {
	while($row = mysql_fetch_array($result)) {
		$ret['now'] = $row['now'];
		$ret['expiry_date'] = $row['expiry_date'];
		$ret['M1_time'] = $row['now'];
	}
}
if ($M0) { // record ingredients
	// var_dump($comp) ;
	foreach ($comp['items'] as $item) {
		$component_id = $item['cid'];
		$M0_temp = $item['temp'];
		$menu_item_component_id = $item['id'];
		$sql = "insert into INGREDIENTS ";
		$sql .= "(id, user_id, menu_item_component_id, component_id,subcomponent_id,M0_time,M0_temp) ";
		$sql .= "values (null,".$userID.",".$menu_item_component_id.",".$ret['id'].",".$component_id.",now(),".$M0_temp.")";
		test_mysql_query($sql);
	}
}
	
$json = json_encode($ret);
echo $json;
// echo $sql."\n\n";
// 
 
?>