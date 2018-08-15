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
if ($prep_type = '') {
	$prep_type = 1;
}
$M1_temp = $comp['M1_temp'];
$M1_chef_id = $comp['M1_chef_id'];


$userID = $_SESSION['userID'];

$sql = "insert into COMPONENT ";
$sql .= "(id, description, prep_type_id, M1_check_id, M1_temp, M1_time, M1_chef_id) ";
$sql .= "values (null,'".$description."',".$prep_type.",".$userID.",".$M1_temp.",now(),".$M1_chef_id.")";
echo $sql."\n\n";
test_mysql_query($sql);
 
?>