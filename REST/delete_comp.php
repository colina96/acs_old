<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
echo "delete_comp.php";
$comp = json_decode($_POST["data"],true);
print("php got comp: " . sizeof($comp));
 
$id = $comp['id'];

$userID = $_SESSION['userID'];

$sql = "delete from COMPONENT ";
$sql .= ' where id='.$id;
test_mysql_query($sql);
 
?>
