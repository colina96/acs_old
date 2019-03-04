<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];

$comp = json_decode($_POST["data"],true);
 
$description = mysql_escape_string($comp['description']);
$prep_type = $comp['prep_type'];
$dock = empty($comp['dock']) ? null:$comp['dock'];
if ($prep_type == '') {
	$prep_type = 1;
}

$M1_chef_id = empty($comp['M1_chef_id']) ? 'null':$comp['M1_chef_id'];
$comp_id = $comp['comp_id'];
$shelf_life_days = empty($comp['shelf_life_days']) ? 0 : $comp['shelf_life_days'];
$M1_action_code = 'null';
$M1_action_code = empty($comp['M1_action_code']) ? 'null':$comp['M1_action_code'];
$M1_action_id = empty($comp['M1_action_id']) ? 'null':$comp['M1_action_id'];
$finished = empty($comp['finished']) ? 'null':'now()';
$M0 = false;	


$userID = $_SESSION['userID'];
$expiry_date = "DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY)";
if (!empty($comp['entered_expiry_date'])) $expiry_date = "'".$comp['entered_expiry_date']."'";
if (!empty($comp['M1_temp'])) {
	$M1_temp = $comp['M1_temp'];
	$sql = "INSERT INTO COMPONENT "
	    . "(comp_id,description, prep_type_id, started, M1_check_id, M1_temp, M1_time, M1_chef_id,M1_action_code,M1_action_id,shelf_life_days,expiry_date,finished) "
	    . "values (".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.",".$M1_temp.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",".$shelf_life_days
	    . ",".$expiry_date.",".$finished.")";
}
else if (!empty($comp['finished'])) {
		$sql = "INSERT into COMPONENT "
        . "(comp_id,description, prep_type_id, started, M1_check_id, M1_time, M1_chef_id,M1_action_code,M1_action_id,finished,shelf_life_days,expiry_date) "
        . "values (".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.",now(),".$M1_chef_id.",".$M1_action_code.",".$M1_action_id.",now(),".$shelf_life_days
        . ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY))";
}
else { // can only be M0 - 
	$sql = "insert into COMPONENT "
        . "(comp_id,description, prep_type_id,started, M1_check_id) "
        . "values (".$comp_id.",'".$description."',".$prep_type.",now(),".$userID.")";
	$M0 = true;
}

test_mysql_query($sql);
$ret = array();
$ret['id'] = mysql_insert_id();
$ret['description'] = $description;
$ret['comp_id'] = $comp_id;
$ret['dock'] = $dock;
$ret['sql'] = $sql;
// $ret['XXexpiry_date'] = empty($comp['expiry_date']) ? 'null':$comp['expiry_date'];
$result = mysql_query("select now() as now,DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY) as expiry_date");
if ($result) {
	while($row = mysql_fetch_array($result)) {
		$ret['now'] = $row['now'];
		$ret['expiry_date'] = $row['expiry_date'];
		if (!empty($comp['entered_expiry_date'])) $ret['expiry_date'] = "'".$comp['entered_expiry_date']."'"; // need for labels
		$ret['M1_time'] = $row['now'];
	}
}

if ($M0) { // record ingredients
	// var_dump($comp) ;
	if (!empty($comp['items'])) {
		foreach ($comp['items'] as $item) {
	
			$component_id = $item['cid'];
			$M0_temp = $item['temp'];
			$subcomponent_id = $item['id'];
	
			$sql = "insert into INGREDIENTS "
	            . "(user_id, component_id,subcomponent_id,M0_time,M0_temp) "
	            . "values (".$userID.",".$subcomponent_id.",".$ret['id'].",now(),".$M0_temp.")";
			test_mysql_query($sql);
		}
	}
}
	
$json = json_encode($ret);
echo $json;
// echo $sql."\n\n";
// 
 
?>
