<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];

	$comp = json_decode($_POST["data"],true);
	print("php got comp: " . sizeof($comp));

	$dish_name = $comp['dish_name'];
	$code = $comp['code'];
	$menu_id = $comp['menu_id'];
	
	$sql = "insert into MENU_ITEMS (id,menu_id,dish_name,code) ";
	$sql .= "values (null,".$menu_id.",'".$dish_name."','".$code."')";
	test_mysql_query($sql);


?>
