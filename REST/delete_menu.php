<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
$userID = $_SESSION['userID'];
$menu_id = get_url_token('menu_id');
// echo "userID ".$userID."\n";
if ($userID > 0 && $menu_id > 0) {

	$sql = "delete from MENUS ";
	$sql .= ' where id='.$menu_id;
	echo $sql;
	test_mysql_query($sql);
	$sql = "delete from MENU_ITEMS ";
	$sql .= ' where menu_id='.$menu_id;
	test_mysql_query($sql);echo $sql;
	$sql = "delete from MENU_ITEM_COMPONENTS ";
	$sql .= ' where menu_id='.$menu_id;
	test_mysql_query($sql);echo $sql;
	$sql = "delete from MENU_ITEM_LINK ";
	$sql .= ' where menu_id='.$menu_id;
	test_mysql_query($sql);echo $sql;
	$sql = "delete from COMPONENT_LINK ";
	$sql .= ' where menu_id='.$menu_id;
	test_mysql_query($sql);echo $sql;
}
 
?>