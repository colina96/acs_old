<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];

if (empty($_POST["data"])) { echo ('no data'); return; }
$data = json_decode($_POST["data"],true);
// var_dump($data['json']);
upload_suppliers($data['json']);

function upload_suppliers($data)
{
	$suppliers = 
	$comps = get_indexed_table('MENU_ITEM_COMPONENTS','where label_at_dock = 1','description');
	$suppliers = get_indexed_table('SUPPLIERS','','name');
	$purchase_orders = get_indexed_table('PURCHASE_ORDERS','','comment'); //temporary hack to get some data into the system for testing
	//var_dump($comps);
	$n = 0;
	$purchase_order_id = -1;
	foreach ($data as $i => $row) {
		for ($i = 0; $i < 3; $i ++) {
			if (array_key_exists($i,$row)) $row[$i] = trim($row[$i]);
			else $row[$i] = '';
		}
		if ($n++ > 0) {
			if ($row[0] == '') { // supplier
				if ($row[1] != '' && $row[2] == '') {
					$supplier_name = $row[1];
					$supplier_id = -1;
					if (array_key_exists($supplier_name,$suppliers)) {
						$supplier_id = $suppliers[$supplier_name]['id'];
						$purchase_order_id = -1; // don't create a new purchase_order
					}
					else {
						$supplier_id = get_supplier_id($supplier_name);
						$purchase_order_id = get_purchase_order_id($supplier_name,$supplier_id); // create a new dummy purchase order for this supplier
					}
					echo 'Supplier -'.$supplier_id.': '.$supplier_name.'<br>';
				}
				else if ($row[1] != '') {
					$description = $row[1];
					$comp_id = -1;
					if (!empty($comps[$description])) {
						$comp_id = $comps[$description]['id'];
						//echo 'found !!!<br>';
					}
					else {
						$comp_id = get_component_id($description, 0, 3, 6 /* FRESH */, 1);
					}
					if ($purchase_order_id > 0) { // create purchase_order_item
						$sql = 'insert into PURCHASE_ORDER_ITEMS (id,purchase_order_id,menu_item_component_id,spec,item_code,prep_type)';
						$sql .= ' values (null,'.$purchase_order_id.','.$comp_id.',"'.$row[0].'","'.$row[2].'",6)';
						test_mysql_query($sql);
					}
					echo 'Component -'.$comp_id.': '.$row[0].'-'.$row[1].'-'.$row[2].'<br>';
				}
			}
			else if ($row[1] != '') {
				$description = $row[1];
				
				if (!empty($comps[$description])) {
					$comp_id = $comps[$description]['id'];
					// echo 'found !!!<br>';
				}
				else {
					$comp_id = get_component_id($description, 0, 3, 6 /* FRESH */, 1);
				}
				echo 'purchase_order_id -'.$purchase_order_id;
				if ($purchase_order_id > 0) { // create purchase_order_item
					$sql = 'insert into PURCHASE_ORDER_ITEMS (id,purchase_order_id,menu_item_component_id,spec,item_code,prep_type)';
					$sql .= ' values (null,'.$purchase_order_id.','.$comp_id.',"'.$row[0].'","'.$row[2].'",6)';
					test_mysql_query($sql);
				}
				echo 'Component -'.$comp_id.': '.$row[0].'-'.$row[1].'-'.$row[2].'<br>';
			}
		}
	}
	// $comp_id = get_component_id($description,0,3,6,1);

}

function get_component_id($description,$menu_id,$shelflife,$preptype,$label_at_dock)
{	
		echo "new component ".$description;
		$shelflife = intval($shelflife);
		$preptype = intval($preptype);
		$sql = "insert into MENU_ITEM_COMPONENTS ";
		$flds = "(id,menu_id,description,shelf_life_days,label_at_dock,prep_type)";
		$vals = " values (null,".$menu_id;
		$vals .= ",'".$description."'";
		$vals .= ",".$shelflife;
		$vals .= ",".$label_at_dock;
		$vals .= ",".$preptype;
		$vals .= ")";
		$sql .= $flds.$vals;
		//	echo $sql;
		test_mysql_query($sql);
		$comp_id = mysql_insert_id();
		return ($comp_id);
}

function get_supplier_id($name)
{
	echo "new supplier ".$name;
	
	$sql = "insert into SUPPLIERS ";
	$flds = "(id,name,lastchange)";
	$vals = " values (null";
	$vals .= ",'".$name."'";
	$vals .= ",now())";
	$sql .= $flds.$vals;
	//	echo $sql;
	test_mysql_query($sql);
	$id = mysql_insert_id();
	return($id);
}
function get_purchase_order_id($comment,$supplier_id)
{
	// echo "new supplier ".$name;

	$sql = "insert into PURCHASE_ORDERS ";
	$flds = "(id,supplier_id,comment,date_created,lastchange)";
	$vals = " values (null,".$supplier_id;
	$vals .= ",'".$comment."'";
	$vals .= ",now(),now())";
	$sql .= $flds.$vals;
	//	echo $sql;
	test_mysql_query($sql);
	$po_id = mysql_insert_id();
	return($po_id);
}
?>
