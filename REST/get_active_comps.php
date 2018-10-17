<?php
session_start();

include '../db.php';
// error_log("running get_active_components",0);
$userID = $_SESSION['userID'];

// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = get_fieldnames("COMPONENT");
	
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
			}
			$comps[] = $comp;
			
		}
		$json = json_encode($comps);
		if ($json) {
			echo $json;
			error_log($json,0);
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
	error_log ("not logged in",0);
}




