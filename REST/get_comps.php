<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = array();
	$types = array();
	$component_links = get_table("COMPONENT_LINK",null);
	// add components to menu_item_components
	
	$result = mysql_query("show columns from MENU_ITEM_COMPONENTS");
	
	while ($row = mysql_fetch_array($result)) {
	
		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}
	$sql = "select * from MENU_ITEM_COMPONENTS";
	$menu_id = get_url_token('menu_id');
	if (!empty($menu_id) && $menu_id > 0) { 
		$sql .= " where menu_id=".$menu_id; 
	}
	$result = mysql_query($sql);
	$comps = array();
	if ($result) {
	
		while($row = mysql_fetch_array($result))
		{
			$comp = array();
			foreach ($fieldnames as $f) {
				$comp[$f] = utf8_encode($row[$f]);
			}
			$comp['label'] = utf8_encode($row['description']);
			$comp['value'] = $row['id'];
			$subs = find_ingredients($component_links,$row['id']);
			if ($subs != null) $comp['subcomponents'] = $subs;
			$comps[] = $comp;
			
		}
		$json = json_encode($comps);
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

function find_ingredients($component_links,$menu_item_component_id)
{
	$ret = null;
	foreach ($component_links as $link) {
		$component_id = $link['component_id'];
		$subcomponent_id = $link['subcomponent_id'];
		if ($component_id == $menu_item_component_id) {
			if (empty($ret)) {
				$ret = array();
			}
			$ret[] = $subcomponent_id;
		}
	}
	return ($ret);
}



