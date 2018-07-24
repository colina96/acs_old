<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from COMPONENT");

	while ($row = mysql_fetch_array($result)) {
		
		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}
	
	$sql = "select * from COMPONENT";
	$result = mysql_query($sql);
	$comps = array();
	if ($result) {
		
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
		}
		else {
			echo "json_encode failed<br>";
		}
		
	}
	else {
		echo mysql_error();
	}
}




