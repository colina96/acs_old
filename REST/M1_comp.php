<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
echo "new_comp.php";
$comp = json_decode($_POST["data"],true);
echo "||||".$_POST["data"]."\r\n";
// var_dump($_POST);
print("php got comp: " . sizeof($comp)."\r\n");
 
$id = $comp['id'];
$M1_temp = $comp['M1_temp'];
$M1_chef_id = $comp['M1_chef_id'];

$userID = $_SESSION['userID'];

$sql = "update COMPONENT ";
$sql .= "set M1_temp = " . $M1_temp . ", M1_chef_id=".$M1_chef_id;
$sql .= ",M1_check_id = ".$_SESSION['userID'];
$sql .= ",M1_time = now()";
if (!empty($comp['finished'])) {
	$sql .= ",finished = now()";
}
$sql .= ' where id='.$id;
echo $sql."\r\n";
test_mysql_query($sql);
 
?>
