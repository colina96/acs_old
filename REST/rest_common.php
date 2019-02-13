<?php

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

function get_comp_details($row,$fieldnames,$qa,$users,$prep_types) 
{
	$comp = array();
	foreach ($fieldnames as $f) {
		if ($f == 'expired') {
			// ignore
		}
		else {
			if (!empty($row[$f]) && $row[$f] != '') // ignore empty fields - which is most
				$comp[$f] = utf8_encode($row[$f]);
		}
		// $comp[$f] = utf8_encode($row[$f]);
		if ($f == 'prep_type_id') {
				
			$id = utf8_encode($row[$f]);
			$comp['prep_type'] = (!empty($prep_types[$id]))?$prep_types[$id]['code']:'-';
		}
		else if (strpos($f,"_id") > 2 && !empty($row[$f]) && $row[$f] != '') {
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
	if (!empty($qa[$M1_action_code])) $comp['M1_action_text'] = $qa[$M1_action_code]['action_text'];
	if (!empty($qa[$M2_action_code])) $comp['M2_action_text'] = $qa[$M2_action_code]['action_text'];
	if (!empty($qa[$M3_action_code])) $comp['M3_action_text'] = $qa[$M3_action_code]['action_text'];
	$comp['state'] = 'M1';
	if ($row['M1_time']) $comp['state'] = 'M2';
	if ($row['M2_time']) $comp['state'] = 'M3';
	if ($row['finished']) $comp['state'] = 'finished';
	return $comp;
}
?>
