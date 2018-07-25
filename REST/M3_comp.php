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
$M3_temp = $comp['M3_temp'];
$M3_chef_id = $comp['M3_chef_id'];


$userID = $_SESSION['userID'];

$sql = "update COMPONENT ";
$sql .= "set M3_temp = " . $M3_temp . ", M3_chef_id=".$M3_chef_id;
$sql .= ",M3_check_id = ".$_SESSION['userID'];
$sql .= ",M3_time = now()";
$sql .= ' where id='.$id;
echo $sql."\n\n";
test_mysql_query($sql);
 
?>
