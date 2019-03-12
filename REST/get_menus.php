<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];

$timeCat = get_url_token('type');
//echo $timeCat;

// By default it returns the current menus if no Type is specified
$sqlTime = "where start_date <= CURDATE() AND end_date >= CURDATE() " ;

if ($timeCat == "future" ) {
	$sqlTime = "where start_date > CURDATE()";
} else if ($timeCat == "expired") {
	$sqlTime = "where start_date < CURDATE() AND end_date < CURDATE()";
} 


// echo "userID ".$userID."\n";
if ($userID > 0) {
	
	$fieldnames = get_fieldnames("MENUS");

	$sql = "select * from MENUS ".$sqlTime;	//add the condition for time period
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




