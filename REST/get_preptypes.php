<?php
session_start();

include '../db.php';

$userID = $_SESSION['userID'];
// echo "userID ".$userID."\n";
if ($userID > 0) {
	
	$sql = "select * from PREP_TYPES order by ID";
	$result = mysql_query($sql);
	$p = array();
	$flds = ['id','code','days_offset','M1_temp',
			'M1_temp_above','M2_time_minutes','M2_alarm_min','M2_temp','M2_temp_above','M3_time_minutes',
			'M3_alarm_min','M3_temp','M3_temp_above','shelf_life_days','probe_type'];
	if ($result) {
		while($row = mysql_fetch_array($result))
		{
			$prep_type = array();
			foreach ($flds as $fld) {
				$prep_type[$fld] = $row[$fld];
			}
			$p[] = $prep_type;
			
		}
		$json = json_encode($p);
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




