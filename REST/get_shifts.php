<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
$table_name = "MENU_ITEMS";
if ($userID > 0) {
	$menu_id = get_url_token('menu_id');
	$shift_data = load_shift_data($menu_id);
	$fieldnames = get_fieldnames($table_name);
	$shift_data = load_shift_data($menu_id);
	$sql = "select * from MENU_ITEMS";
	if (!empty($menu_id) && $menu_id > 0) {
		$sql .= " where menu_id=".$menu_id;
	}
	// echo $sql;
	$result = mysql_query($sql);
	$items = array();
	if ($result) {
	
		while($row = mysql_fetch_array($result))
		{
			$item = array();
			
			$item['id'] = $row['id'];
			$item['menu_id'] = $row['menu_id'];
			$item['dish_name'] = utf8_encode($row['dish_name']);
			$item['code'] = $row['code'];
			if (!empty($shift_data[$item['id']])) {
				// var_dump($shift_data[$item['id']]);
				// $item['shift_data'] = $shift_data[$item['id']];
				$sd = $shift_data[$item['id']];
				$item['s1'] = $sd[0]['s1'];
				$item['s2'] = $sd[0]['s2'];
				$item['s3'] = $sd[0]['s3'];
				$item['current_shift'] = $shift_data['current_shift'];
				
			}
			$items[] = $item;
		}
		foreach ($items as $item ) {
			if (empty ($shift_data[$item['id']])) {
			//	echo 'no data for '.$item['code'];
				$sql = 'insert into SHIFT_ORDERS (id,menu_id,menu_item_id,s1,s2,s3,sdate) values (null,';
				$sql .= $item['menu_id'].','.$item['id'].',0,0,0,now())';
				test_mysql_query($sql);
			}
		}
	}
	$json = json_encode($items);
	if ($json) {
		echo $json;
	}
	else {
		echo "json_encode failed<br>";
	}		
}

