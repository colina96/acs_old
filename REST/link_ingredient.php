<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
$data = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got comp: " . sizeof($comp));
 $menu_id = $data['menu_id'];
 $component_id = $data['component_id'];
 $subcomponent_id = $data['subcomponent_id'];

$userID = $_SESSION['userID'];

	$sql = "insert into COMPONENT_LINK ";
	$sql .= "(id, menu_id, component_id, subcomponent_id) ";
	$sql .= "values (null,".$menu_id.",".$component_id.",".$subcomponent_id.")";

test_mysql_query($sql);
echo $sql."\n\n";
// 
 
?>