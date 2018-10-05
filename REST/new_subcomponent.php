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
$probe_type = !empty($comp['probe_type'])?$comp['probe_type']:'null';
$location = !empty($comp['location'])?mysql_escape_string($comp['location']):'null';
$shelf_life_days = !empty($comp['shelf_life_days'])?$comp['shelf_life_days']:'null';
$high_risk = !empty($comp['high_risk'])?$comp['high_risk']:0;
$supplier = !empty($comp['supplier'])?mysql_escape_string($comp['supplier']):'null';
$product = !empty($comp['product'])?mysql_escape_string($comp['product']):'null';
$spec = !empty($comp['spec'])?mysql_escape_string($comp['spec']):'null';
$PT_id = !empty($comp['PT_id'])?$comp['PT_id']:'null';

$userID = $_SESSION['userID'];

	$sql = "insert into MENU_ITEM_COMPONENTS ";
	$sql .= "(id, menu_id,description,prep_type,probe_type,location,shelf_life_days,high_risk,supplier,product,spec,PT_id) ";
	$sql .= "values (null,".$menu_id.",'".$description."',".$prep_type.",".$probe_type;
	$sql .= ",'".$location."',".$shelf_life_days.",".$high_risk;
	$sql .= ",'".$supplier."','".$product."','".$spec."',".$PT_id;
	$sql .= ")";

$ret = test_mysql_query($sql);
if ($ret != null) {
$comp = array();
$comp['id'] = mysql_insert_id();
$comp['description'] = $description;
$sql = "insert into COMPONENT_LINK (id,menu_id,component_id,subcomponent_id) values (null,";
$sql .= $menu_id.",".$menu_item_component_id.",".$comp['id'].")";
test_mysql_query($sql);
$json = json_encode($comp);
echo $json;
}
// echo $sql."\n\n";
// 
 
?>