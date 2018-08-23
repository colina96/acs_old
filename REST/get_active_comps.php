<?php
session_start();

include '../db.php';
// error_log("running get_active_components",0);
$userID = $_SESSION['userID'];

// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from COMPONENT");
	if ($result) {
	//	error_log("read columns",0);
		while ($row = mysql_fetch_array($result)) {
			$fieldname = $row['Field'];
			$fieldnames[] = $fieldname;
			$types[$fieldname] = $row['Type'];
			error_log($fieldname,0);
		}
	}
	else {
		error_log("could not read columns",0);
	}
	
	
	$sql = "select * from COMPONENT  where finished is null";
	if (!empty(get_url_token('finished'))) {
		$sql = "select * from COMPONENT  where finished is not null and expiry_date > now()";
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




