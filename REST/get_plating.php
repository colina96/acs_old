<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
$table_name = "PLATING_ITEM";
$sql = "select * from ".$table_name;
$log = 'LOG: ';
$search_terms = null;
if ($userID > 0) {
	$menu_id = get_url_token('menu_id');
	$fieldnames = get_fieldnames($table_name);
	$plating_item_component_flds = get_fieldnames('PLATING_ITEM_COMPONENT');
	if (!empty($_POST["data"])) {
		
		$search_terms = json_decode($_POST["data"],true);
		if (!empty($search_terms)) {
			$log .= 'found search terms';
			if ($search_terms['search_for']) {
				$where = true;
				$sql .= " where ITEM_NAME like '%".$search_terms['search_for']."%'";
			}
			if ($search_terms['start_date']) {
				if (!$where) {
					$sql .= " where ";
					$where = true;
				}
				else {
					$sql .= ' and ';
				}
				$sql .= "time_started > '".$search_terms['start_date']."'";
		
			}
			if ($search_terms['end_date']) {
				if (!$where) {
					$sql .= " where ";
					$where = true;
				}
				else {
					$sql .= ' and ';
				}
				$sql .= "time_started <= '".$search_terms['end_date']." 23:59'";
					
			}
		}
		else {
			$log .= " no search terms";
		} 
	}
	else {
		$sql .= " where user_id = ".$userID;
    	$sql.= " and DATE(time_started) = CURDATE()"; // left to make testing easier - TODO remove
	}
	 // echo $sql;
	$log .= ' sql= '.$sql;
	$result = mysql_query($sql);
	$items = array();
	if ($result) {
		
		while($row = mysql_fetch_array($result))
		{
			$item = array();
			foreach ($fieldnames as $f) {		
				$item[$f] = utf8_encode($row[$f]);
			}
			$items[] = $item;

		}
		// now get plating component data
		for ($i = 0; $i < sizeof($items); $i++) {
			$items[$i]['items'] = array();
			$pc = array();
			//echo "get components for ".$items[$i]['id'];
			$sql = "select * from PLATING_ITEM_COMPONENT where plating_item_id = ".$items[$i]['id'];
			$result = mysql_query($sql);
			while($row = mysql_fetch_array($result)) {
				foreach ($plating_item_component_flds as $f) {
					// echo $f."=>".$row[$f]."<br>\n";
					$pc[$f] = utf8_encode($row[$f]);
				}
				$items[$i]['items'][] = $pc;
			}
			// echo json_encode($pc)."<br><hr>";
			
		}
		$ret = array();
		$ret['log'] = $log;
		$ret['items'] = $items;
	
		$ret['sql'] = $sql;
		$ret['search'] = $search_terms;
		$json = json_encode($items);
		// $json = json_encode($ret);
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

else {
	$ret = array();
	$ret['error'] = 'not logged in';
	echo json_encode($ret);
	error_log ("not logged in",0);
}



