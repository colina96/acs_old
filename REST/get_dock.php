<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$suppliers = get_table("SUPPLIERS",""); 
	$prep_types = get_table('PREP_TYPES','');
	$pos = get_table('PURCHASE_ORDERS','where date_received is null');
	$pois = get_table('PURCHASE_ORDER_ITEMS','where date_received is null');
	$component_list = null;
	foreach ($pois as $poi) { // attach items to purchase orders
		$po_id = $poi['purchase_order_id'];
		if ($pos[$po_id]) {
			if (empty($pos[$po_id]['items'])) $pos[$po_id]['items'] = array();
			$pos[$po_id]['items'][] = $poi;
		}
		if (empty($component_list)) $component_list = '';
		else $component_list .= ',';
		$component_list .= $poi['menu_item_component_id'];
		
	}
	// $comps = get_table('MENU_ITEM_COMPONENTS','where id in ('.$component_list.')');
	$comps = get_table('MENU_ITEM_COMPONENTS','where label_at_dock = 1');
	foreach ($pois as $poi) { // attach component details to purchase order items
		if (!empty($comps[$poi['menu_item_component_id']])) $pois[$poi['id']]['menu_item_component'] = $comps[$poi['menu_item_component_id']];
	}
	
	foreach ($pos as $i => $po) { // attach items to purchase orders
		//echo '----------------';
		//var_dump($po);
		// echo $po['supplier_id'];
		// if (!empty($suppliers[$po['supplier_id']])) $pos[$po['id']]['supplier'] = $suppliers[$po['supplier_id']];
		if (!empty($suppliers[$po['supplier_id']])) $pos[$i]['supplier'] = $suppliers[$po['supplier_id']];
		foreach ($po['items'] as $j => $poi) {
			$comp_id = $poi['menu_item_component_id'];
			$pos[$i]['items'][$j]['component'] = $comps[$comp_id];
			$pos[$i]['items'][$j]['PT'] = $prep_types[$poi['prep_type']];
		}
	}
	$ret = array();

	
	$ret['purchase_orders'] = $pos;
	$ret['prep_types'] = $prep_types;
 	$ret['comps'] = $comps;
	$ret['suppliers'] = $suppliers;
	echo json_encode($ret);
}




