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
if ($prep_type == '') {
	$prep_type = 1;
}

$M1_chef_id = $comp['M1_chef_id'];
$shelf_life_days = $comp['shelf_life_days'];


$userID = $_SESSION['userID'];
if (!empty($comp['M1_temp'])) {
	$M1_temp = $comp['M1_temp'];
	$sql = "insert into COMPONENT ";
	$sql .= "(id, description, prep_type_id, M1_check_id, M1_temp, M1_time, M1_chef_id,shelf_life_days,expiry_date) ";
	$sql .= "values (null,'".$description."',".$prep_type.",".$userID.",".$M1_temp.",now(),".$M1_chef_id.",".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY))";
}
else if (!empty($comp['finished'])) {
	$sql = "insert into COMPONENT ";
	$sql .= "(id, description, prep_type_id, M1_check_id, M1_time, M1_chef_id,finished,shelf_life_days,expiry_date) ";
	$sql .= "values (null,'".$description."',".$prep_type.",".$userID.",now(),".$M1_chef_id.",now(),".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY))";
}
test_mysql_query($sql);
$comp = array();
$comp['id'] = mysql_insert_id();
$comp['description'] = $description;
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