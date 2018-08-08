<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from USERS");
	
	while ($row = mysql_fetch_array($result)) {
	
		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}
	$sql = "select * from USERS";
	$result = mysql_query($sql);
	$comps = array();
	if ($result) {
	
		while($row = mysql_fetch_array($result))
		{
			$comp = array();
			foreach ($fieldnames as $f) {
				$comp[$f] = utf8_encode($row[$f]);
			}
			$comp['label'] = utf8_encode($row['firstname'].' '.$row['lastname']);
			$comp['value'] = $row['id'];
			$comps[] = $comp;
	
		}
		$json = json_encode($comps);
		if ($json) {
			echo $json;
		}
		else {
			echo "json_encode failed<br>";
		}
	
	}
	else {
		echo mysql_error();
	}
}




