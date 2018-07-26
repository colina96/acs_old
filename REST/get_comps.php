<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = array();
	$types = array();
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
	/*
	if ($result) {
		
		while($row = mysql_fetch_array($result))
		{
			
			$d =  $row['description'];
			if ($d && strlen($d) > 0) {
				$comp = array();
				$comp['value'] = $row['id'];
				$comp['label'] = utf8_encode($d);
			
				// $comps[] = $comp;
				$comps[] = utf8_encode($d);
			}
			//$found++;
			
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
	} */
}




