<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
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

$M1_chef_id = $comp['M1_chef_id'];
$comp_id = $comp['comp_id'];
$shelf_life_days = empty($comp['shelf_life_days']) ? 0 : $comp['shelf_life_days'];
$M1_action_code = 'null';
$M1_action_code = empty($comp['M1_action_code']) ? 'null':$comp['M1_action_code'];
$M1_action_id = empty($comp['M1_action_id']) ? 'null':$comp['M1_action_id'];
$finished = empty($comp['finished']) ? 'null':'now()';
	


$userID = $_SESSION['userID'];
if (!empty($comp['M1_temp'])) {
	$M1_temp = $comp['M1_temp'];
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id, M1_check_id, M1_temp, M1_time, M1_chef_id,M1_action_code,M1_action_id,shelf_life_days,expiry_date,finished) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",".$userID.",".$M1_temp.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY),".$finished.")";
}
else if (!empty($comp['finished'])) {
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id, M1_check_id, M1_time, M1_chef_id,M1_action_code,M1_action_id,finished,shelf_life_days,expiry_date) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",".$userID.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",now(),".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY))";
}
test_mysql_query($sql);
$comp = array();
$comp['id'] = mysql_insert_id();
$comp['description'] = $description;
$comp['comp_id'] = $comp_id;
$comp['dock'] = $dock;
$result = mysql_query("select now() as now,DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY) as expiry_date");
if ($result) {
	while($row = mysql_fetch_array($result)) {
		$comp['now'] = $row['now'];
		$comp['expiry_date'] = $row['expiry_date'];
	}
}
	
$json = json_encode($comp);
echo $json;
// echo $sql."\n\n";
// 
 
?>