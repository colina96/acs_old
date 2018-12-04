<?php
session_start();


// unlinks a menu item component from a menu_item
include '../db.php';
$con = $GLOBALS['con'];

$comp = json_decode($_POST["data"],true);
print("php got comp: " . sizeof($comp));
 
$component_id = $comp['component_id'];
$menu_item_id = $comp['menu_item_id'];
$menu_id = $comp['menu_id'];



$userID = $_SESSION['userID'];

$sql = "delete from MENU_ITEM_LINK ";
$sql .= ' where menu_item_id='.$menu_item_id;
$sql .= ' and component_id='.$component_id;
$sql .= ' and menu_id='.$menu_id;
test_mysql_query($sql);
 
?>