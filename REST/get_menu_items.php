<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
$table_name = "MENU_ITEMS";
if ($userID > 0) {
	$menu_id = get_url_token('menu_id');
	$fieldnames = get_fieldnames($table_name);
	$menu_item_links = load_menu_item_links($menu_id);
	$sql = "select * from MENU_ITEMS";
	
	if (!empty($menu_id) && $menu_id > 0) { 
		$sql .= " where menu_id=".$menu_id; 
	}
	$result = mysql_query($sql);
	$items = array();
	if ($result) {
		
		while($row = mysql_fetch_array($result))
		{
			$item = array();
			foreach ($fieldnames as $f) {		
				$item[$f] = utf8_encode($row[$f]);
			}
			$item['label'] = $row['code'].' '.utf8_encode($row['dish_name']);
			$item['value'] = $row['id'];
			$menu_item_id = $row['id'];
			
			if (isset($menu_item_links[$menu_item_id])) {
				$item['items'] = $menu_item_links[$menu_item_id];
			}
			$items[] = $item;

		}
		$json = json_encode($items);
		if ($json) {
			echo $json;
		}
		else {
			echo "json_encode failed<br>";
		}
		
	}
	else {
		echo mysql_error();
	}
}


function load_menu_item_links($menu_id)
{
	$comp_fields = get_fieldnames("MENU_ITEM_COMPONENTS");
	$menu_item_links = Array();
	$sql = "select * from MENU_ITEM_LINK,MENU_ITEM_COMPONENTS";
	$menu_id = get_url_token('menu_id');
	if (!empty($menu_id) && $menu_id > 0) {
		$sql .= " where menu_id=".$menu_id;
		$sql .= " and MENU_ITEM_LINK.component_id = MENU_ITEM_COMPONENTS.id";
	}
	else {
		$sql .= " where MENU_ITEM_LINK.component_id = MENU_ITEM_COMPONENTS.id";
	}
	//echo $sql;
	$result = mysql_query($sql);
	if ($result) {
		while($row = mysql_fetch_array($result))
		{
			$menu_item_id = $row['menu_item_id'];
			if (!isset($menu_item_links[$menu_item_id])) {
				$menu_item_links[$menu_item_id] = [];
			}
			$component_id = $row['component_id'];
			$d = $row[5]; // because php isn't good at joins
			//echo $d;
			$comp = [];
			$comp['id'] = $component_id;
			$comp['description'] = utf8_encode($d);
			$menu_item_links[$menu_item_id][] = $comp; // $component_id;
		}
	}
	return($menu_item_links);
}

