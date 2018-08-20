<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
echo "new_comp.php";
$comp = json_decode($_POST["data"],true);
echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
print("php got comp: " . sizeof($comp));
 
$id = $comp['id'];
$M2_temp = $comp['M2_temp'];
$M2_chef_id = $comp['M2_chef_id'];


$userID = $_SESSION['userID'];

$sql = "update COMPONENT ";
$sql .= "set M2_temp = " . $M2_temp . ", M2_chef_id=".$M2_chef_id;
$sql .= ",M2_check_id = ".$_SESSION['userID'];
$sql .= ",M2_time = now()";
if (!empty($comp['finished'])) {
	$sql .= ",finished = now()";
}
$sql .= ' where id='.$id;
echo $sql."\n\n";
test_mysql_query($sql);
 
?>
