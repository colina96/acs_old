<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
echo "new_comp.php";
$comp = json_decode($_POST["data"],true);
echo "||||".$_POST["data"]."XXXX\n\n";
var_dump($_POST);
print("php got comp: " . sizeof($comp));
 
$description = mysql_escape_string($comp['description']);
$prep_type = $comp['prep_type'];
if ($prep_type == '') {
	$prep_type = 1;
}
$M1_temp = $comp['M1_temp'];
$M1_chef_id = $comp['M1_chef_id'];


$userID = $_SESSION['userID'];
if (!empty($comp['M1_temp'])) {
	$sql = "insert into COMPONENT ";
	$sql .= "(id, description, prep_type_id, M1_check_id, M1_temp, M1_time, M1_chef_id) ";
	$sql .= "values (null,'".$description."',".$prep_type.",".$userID.",".$M1_temp.",now(),".$M1_chef_id.")";
}
else if (!empty($comp['finished'])) {
	$sql = "insert into COMPONENT ";
	$sql .= "(id, description, prep_type_id, M1_check_id, M1_time, M1_chef_id,finished) ";
	$sql .= "values (null,'".$description."',".$prep_type.",".$userID.",now(),".$M1_chef_id.",now())";
}
echo $sql."\n\n";
test_mysql_query($sql);
 
?>