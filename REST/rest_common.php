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
?>