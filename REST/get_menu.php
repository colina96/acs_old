<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
$menu_id = get_url_token('menu_id');
// $menu_id = 2;
// echo "userID ".$userID."\n";
$data = array();
if ($userID > 0) {
	$data['menu'] = get_table("MENUS"," where id = ".$menu_id);
	$menu_items = get_table("MENU_ITEMS"," where menu_id = ".$menu_id);
	// $menu_item_components = get_table("MENU_ITEM_COMPONENTS"," where menu_id = ".$menu_id);
	$menu_item_components = get_table("MENU_ITEM_COMPONENTS",""); 
	// get everything - this will have to be fixed at some point when we know more about how often componets show up
	$menu_item_link = get_table("MENU_ITEM_LINK"," where menu_id = ".$menu_id);
	$component_links = get_table("COMPONENT_LINK"," where menu_id = ".$menu_id);
	// add components to menu_item_components
	foreach ($component_links as $link) {
		$component_id = $link['component_id'];
		$subcomponent_id = $link['subcomponent_id'];
		if (!empty($menu_item_components[$component_id])) {
			if (empty($menu_item_components[$component_id]['subcomponents'])) {
				$menu_item_components[$component_id]['subcomponents'] = array();
			}
			$menu_item_components[$component_id]['subcomponents'][] = $subcomponent_id;
		}
	}
	foreach ($menu_item_link as $link) {
		$menu_item_id = $link['menu_item_id'];
		$component_id = $link['component_id'];
		if (!empty($menu_items[$menu_item_id])) {
			if (empty($menu_items[$menu_item_id]['components'])) {
				$menu_items[$menu_item_id]['components'] = array();
			}
			$menu_items[$menu_item_id]['components'][] = $component_id;
		}
	}
//	echo "adding labels";
	foreach ($menu_item_components as $c) {
		$id = $c['id'];
		// echo " " . $c['id'].' '.$c['description'];
		$menu_item_components[$id]['value'] = $id;
		$menu_item_components[$id]['label'] = $c['description'];
	}
	$data['menu_items'] = $menu_items;
	$data['menu_item_components'] = $menu_item_components;
	$data['component_links'] = $component_links;
	$data['preptypes'] = get_table("PREP_TYPES",null);
	$json = json_encode($data);
	if ($json) {
		echo $json;
	}
	else {
		echo "json_encode failed<br>";
	}
}




