<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
// echo "new_comp.php";
$user = json_decode($_POST["data"],true);
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from "."USERS");
	while ($row = mysql_fetch_array($result)) {

		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}

	
	$sql = "replace INTO USERS ";
	$n = 0;
	$flds = '(';
	$vals = ') values (';
	foreach ($fieldnames as $i => $fieldname) {
		if ($fieldname == "id") {
			if ($user['id'] > 0) $vals .= $user['id'];
			else $vals .= 'null';
			$flds .= "id";
		}
		else if (substr($types[$fieldname],0,7) == "varchar" )
		{
			
			//$sql .= $fieldname."='".mysql_escape_string( $user[$fieldname])."'";
			$vals .= ",'".mysql_escape_string( $user[$fieldname])."'";
			$flds .= ",".$fieldname;
		}
		else if ($types[$fieldname] == "tinyint(1)") {
			
			if (empty($user[$fieldname]) || $user[$fieldname] != 1) {
				$vals .= ",false";
				// $sql .= $fieldname."=false";
			}
			else {
				$vals .= ",true";
				// $sql .= $fieldname."=true";
			}
			$flds .= ",".$fieldname;
		
		}
		else {
			echo "unknown fieldtype ".$types[$fieldname];
		}
	}
	$sql .= $flds.$vals.')';
	// $sql .= " where ID=".$user['id'];
	
	test_mysql_query($sql); 
	echo $sql;
?>
