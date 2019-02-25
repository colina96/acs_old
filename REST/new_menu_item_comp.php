<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];

	$comp = json_decode($_POST["data"],true);
	print("php got comp: " . sizeof($comp));

	$description = $comp['description'];
	$menu_item_id = $comp['menu_item_id'];
	$menu_id = $comp['menu_id'];


	$comp_id = get_component_id($description,$menu_id,null,'','',0,null,0);
	
	$sql = "insert into MENU_ITEM_LINK (id,menu_id,menu_item_id,component_id) ";
	$sql .= "values (null,".$menu_id.",".$menu_item_id.",".$comp_id.")";
test_mysql_query($sql);

function get_component_id($description,$menu_id,$shelflife,$preptype,$track)
{
	$sql = "select * from MENU_ITEM_COMPONENTS where description='".$description."' and menu_id=".$menu_id;
	$result = mysql_query($sql);
	$comp_id = -1;
	while ($row = mysql_fetch_array($result) ) {
		$comp_id = $row['id'];
		// insert new component
	}
	if ($comp_id == -1) {
		$shelflife = intval($shelflife);
		$preptype = intval($preptype);
		$sql = "insert into MENU_ITEM_COMPONENTS ";
		$flds = "(id,menu_id,description,shelf_life_days,prep_type)";
		$vals = " values (null,".$menu_id;
		$vals .= ",'".$description."'";
		$vals .= ",".$shelflife;
		$vals .= ",".$preptype;
		$vals .= ")";
		$sql .= $flds.$vals;
		//	echo $sql;
		test_mysql_query($sql);
		$comp_id = mysql_insert_id();
	}
	return ($comp_id);
}

?>