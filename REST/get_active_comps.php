<?php
session_start();

include '../db.php';
// error_log("running get_active_components",0);
$userID = $_SESSION['userID'];

// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = get_fieldnames("COMPONENT");
	$qa = get_qa();
	$users = get_users();
	$prep_types = get_prep_types();
	$fieldnames[] = 'expired';
	$sql = "select *,(expiry_date < now()) as expired from COMPONENT  where finished is null and M1_check_id=".$userID;
	if (!empty(get_url_token('finished'))) {
		$sql = "select *,(expiry_date < now()) as expired from COMPONENT  where finished is not null";
	}
	else {
		
	}
	if (!empty(get_url_token('all'))) {
		$sql = "select *,(expiry_date < now()) as expired  from COMPONENT";
	}
	if (!empty(get_url_token('cid'))) {
		$sql = "select *,(expiry_date < now()) as expired  from COMPONENT";
		$sql .= " where id=".get_url_token('cid');

	}
	$result = mysql_query($sql);
	$comps = array();
	if ($result) {
	//	error_log("read COMPONENT",0);
		while($row = mysql_fetch_array($result))
		{
			$comp = array();
			foreach ($fieldnames as $f) {		
				$comp[$f] = utf8_encode($row[$f]);
				if ($f == 'prep_type_id') {
					
					$id = utf8_encode($row[$f]);
					$comp['prep_type'] = (!empty($prep_types[$id]))?$prep_types[$id]['code']:'-';
				}
				else if (strpos($f,"_id") > 2) {
					//echo $f.'-';
					$fname = substr($f,0,strpos($f,"_id"));
					//echo $fname.', ';
					$id = utf8_encode($row[$f]);
					$comp[$fname] = (!empty($users[$id]))?$users[$id]['label']:'-';
					//$comp[$fname] = 'XXXXX';
					
				}
			}
			$M1_action_code = $row['M1_action_code'];
			$M2_action_code = $row['M2_action_code'];
			$M3_action_code = $row['M3_action_code'];
			$comp['M1_action_text'] = (!empty($qa[$M1_action_code]))?$qa[$M1_action_code]['action_text']:'-';
			$comp['M2_action_text'] = (!empty($qa[$M2_action_code]))?$qa[$M2_action_code]['action_text']:'-';
			$comp['M3_action_text'] = (!empty($qa[$M3_action_code]))?$qa[$M3_action_code]['action_text']:'-';
			$comps[] = $comp;
			
		}
		$ret = array();
		$ret['comps'] = $comps;
		$ret['qa'] = $qa;
		// $json = json_encode($comps);
		$json = json_encode($comps);
		if ($json) {
			echo $json;
		//	error_log($json,0);
		}
		else {
			echo "json_encode failed<br>";
			error_log ("json_encode failed",0);
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

function get_qa()
{
	$result = mysql_query('select * from CORRECTIVE_ACTIONS');
	$qa = array();
	if ($result) {
		//	error_log("read COMPONENT",0);
		while($row = mysql_fetch_array($result))
		{
			$line = array();
			$line['id'] = $row['id'];
			$line['prep_type'] = $row['prep_type'];
			$line['action_text'] = $row['action_text'];
			$qa[$row['id']] = $line;
		}
	}
	return ($qa);
}

function get_users()
{
	$result = mysql_query('select * from USERS');
	$ret = array();
	if ($result) {
		//	error_log("read COMPONENT",0);
		while($row = mysql_fetch_array($result))
		{
			$line = array();
			$line['id'] = $row['id'];
			$line['label'] = utf8_encode($row['firstname'].' '.$row['lastname']);
			$line['value'] = $row['id'];
			$ret[$row['id']] = $line;
		}
	}
	return ($ret);
}

function get_prep_types()
{
	$result = mysql_query('select id,code from PREP_TYPES');
	$ret = array();
	if ($result) {
		while($row = mysql_fetch_array($result))
		{
			$line = array();
			$line['id'] = $row['id'];
			$line['code'] = $row['code'];
			$ret[$row['id']] = $line;
		}
	}
	return ($ret);
}

