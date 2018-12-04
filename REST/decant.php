<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
// echo $_POST["data"];
$comp = json_decode($_POST["data"],true);
// echo "||||".$_POST["data"]."XXXX\n\n";
// var_dump($_POST);
// print("php got comp: " . sizeof($comp));
 
$description = mysql_escape_string($comp['description']);
// $prep_type = $comp['prep_type'];
$dock = empty($comp['dock']) ? null:$comp['dock'];


$M1_chef_id = empty($comp['M1_chef_id']) ? 'null':$comp['M1_chef_id'];
$decanted_from = $comp['id'];
$comp_id = $comp['component_id'];
$shelf_life_days = 6; // get from prep type TODO
$prep_type = 9;
$finished =  'now()';
$M0 = false;	


$userID = $_SESSION['userID'];

//	$M1_temp = $comp['M1_temp'];
	$sql = "insert into COMPONENT ";
	$sql .= "(id, comp_id,description, prep_type_id, M1_check_id,  M1_time,shelf_life_days,expiry_date,finished,decanted_from) ";
	$sql .= "values (null,".$comp_id.",'".$description."',".$prep_type.",".$userID.",now(),".$shelf_life_days;
	$sql .= ",DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY),".$finished.",".$decanted_from.")";
	

test_mysql_query($sql);
$ret = array();
$ret['id'] = mysql_insert_id();
$ret['description'] = $description;
$ret['comp_id'] = $comp_id;

$result = mysql_query("select now() as now,DATE_ADD(now(), INTERVAL ".$shelf_life_days." DAY) as expiry_date");
if ($result) {
	while($row = mysql_fetch_array($result)) {
		$ret['now'] = $row['now'];
		$ret['expiry_date'] = $row['expiry_date'];
		$ret['M1_time'] = $row['now'];
	}
}

$json = json_encode($ret);
echo $json;
// echo $sql."\n\n";
// 
 
?>