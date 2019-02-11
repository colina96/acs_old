<?php
session_start();

include '../db.php';
include 'rest_common.php';
// error_log("running get_active_components",0);
$userID = $_SESSION['userID'];
$search_terms = null;
$log = 'LOG:';
if (!empty($_POST["data"])) {
	$search_terms = json_decode($_POST["data"],true);
}

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
	if (!empty(get_url_token('all')) || !empty($search_terms)) {
		$sql = "SELECT *,(expiry_date < now()) as expired  from COMPONENT";
		$where = false;
		if (!empty($search_terms)) {
			$log .= 'found search terms';
			if ($search_terms['search_for']) {
				$where = true;
				$sql .= " where DESCRIPTION like '%".$search_terms['search_for']."%'";
			}
			if ($search_terms['start_date']) {
				if (!$where) {
					$sql .= " where ";
					$where = true;
				}
				else {
					$sql .= ' and ';
				}
				$sql .= "M1_time > '".$search_terms['start_date']."'";
				
			}
			if ($search_terms['end_date']) {
				if (!$where) {
					$sql .= " where ";
					$where = true;
				}
				else {
					$sql .= ' and ';
				}
				$sql .= "M1_time <= '".$search_terms['end_date']." 23:59'";
					
			}
		}
		else {
			$log .= " no search terms";
		}
	}
	if (!empty(get_url_token('cid'))) {
		$sql = "select *,(expiry_date < now()) as expired  from COMPONENT";
		$sql .= " where id=".get_url_token('cid');

	}
	$sql .= " order by id";
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
		//	$comp['ingredients'] = array();
		//	$comp['ingredients'][] = $comp['id'] + 1;
			$comp['state'] = 'M1';
			if ($row['M1_time']) $comp['state'] = 'M2';
			if ($row['M2_time']) $comp['state'] = 'M3';
			if ($row['finished']) $comp['state'] = 'finished';
			$comps[] = $comp;
			
		}
		$ret = array();
		$ret['log'] = $log;
		$ret['sql'] = $sql;
		// instead of join a join here, get a list of ingredient ids then add them later
		$ingredient_ids = null;
		foreach ($comps as $i => $comp) {
			$sql = 'select * from INGREDIENTS where component_id='.$comp['id'];
			$result = mysql_query($sql);
			
			if ($result) {		
				while($row = mysql_fetch_array($result)) {
					if (empty ($comps[$i]['ingredients'])) $comps[$i]['ingredients'] = array();
					$comps[$i]['ingredients'][] = $row['subcomponent_id'];
					if (empty($ingredient_ids)) $ingredient_ids = '';
					else $ingredient_ids .= ',';
					$ingredient_ids .= $row['subcomponent_id'];
				}
			}
			
		}
		$ingredients = array();
		if (!empty($ingredient_ids)) {
			$sql = "select * from COMPONENT where id in (".$ingredient_ids.")";
			// echo $sql;
			$result = mysql_query($sql);
			if ($result) {
				
				while($row = mysql_fetch_array($result)) {
					$id = $row['id'];
				//	echo "found ingredient ".$id;
					$ingredients[$id] = get_comp_details($row,$fieldnames,$qa,$users) ;
				}
			}
			else {
			//	echo "no result for ".$sql;
			}
		}
		else {
		//	echo "NO INGREDIENTS";
		}
		foreach ($comps as $i => $comp) {
			
			if (!empty ($comps[$i]['ingredients'])) {
				$comps[$i]['subs'] = array();
				foreach ($comps[$i]['ingredients'] as $ing_id) {
					$comps[$i]['subs'][] = $ingredients[$ing_id];
				}
			}
				
		}
		// now insert ingredient details
		
		$ret['comps'] = $comps;
		$ret['qa'] = $qa;
		
		$ret['search'] = $search_terms;
		
		$json = json_encode($comps);
		// $json = json_encode($ret);
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


function get_comp_details($row,$fieldnames,$qa,$users) 
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