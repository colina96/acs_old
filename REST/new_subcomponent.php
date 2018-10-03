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
$menu_id = $comp['menu_id'];
$menu_item_component_id = $comp['menu_item_component_id'];
$prep_type = !empty($comp['prep_type'])?$comp['prep_type']:'5';

$userID = $_SESSION['userID'];

	$sql = "insert into MENU_ITEM_COMPONENTS ";
	$sql .= "(id, menu_id,description,prep_type) ";
	$sql .= "values (null,".$menu_id.",'".$description."',".$prep_type.")";

test_mysql_query($sql);
$comp = array();
$comp['id'] = mysql_insert_id();
$comp['description'] = $description;
$sql = "insert into COMPONENT_LINK (id,menu_id,component_id,subcomponent_id) values (null,";
$sql .= $menu_id.",".$menu_item_component_id.",".$comp['id'].")";
test_mysql_query($sql);
$json = json_encode($comp);
echo $json;
// echo $sql."\n\n";
// 
 
?>