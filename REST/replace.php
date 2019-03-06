<?php 

/*
 * Generic table insert and update REST call. The table name must be in the JSON data. If the ID is not null then
 * that record is updated, otherwise a new row is inserted and the ID of the record returned
 */

session_start();

include '../db.php';

if (empty($_POST["data"])) { echo ('no data'); return; }
$data = json_decode($_POST["data"],true);

if (empty ($data['TABLENAME'])) {
	return ("ERROR: no table name");
}
$table_name = $data['TABLENAME']; 
// $table_name = 'COMPONENT';

$fields = array();
//$types = array();
$result = mysql_query("show columns from ".$table_name);
while ($row = mysql_fetch_array($result)) {
	$fields[] = $row;
}
$ret = array();
$ret['TABLENAME'] = $table_name;
$ret['fields'] = $fields;
if (!empty($data['action'])) {
	$ret['action'] = $data['action'];
	if ($data['action'] == 'INSERT') {
		// build sql
		$ret['sql'] = build_sql($table_name,$fields,$data['data']);
	}
	if ($data['action'] == 'UPDATE') {
		// build sql
		$ret['sql'] = build_update_sql($table_name,$fields,$data['data']);
	}
	if ($data['action'] == 'GET') {
		// build sql
		
		$ret['data'] = get_result($table_name,$fields,empty($data['conditions'])?null:$data['conditions']);
	}
}
echo json_encode($ret);

function get_result($table_name,$fields,$conditions)
{
	$sql = 'select * from '.$table_name;
	if (!empty($conditions)) {
		$sql .= ' where '.$conditions;
	}
	$result = mysql_query($sql);
	$items = array();
	if ($result) {
	
		while($row = mysql_fetch_array($result))
		{
			$item = array();
			foreach ($fields as $field) {
				$fieldname = $field['Field'];
				$item[$fieldname] = utf8_encode($row[$fieldname]);
			}
			$items[] = $item;
	
		}
	}
	return($items);
}
function build_sql($table_name,$fields,$data)
{

	$sql = "replace INTO ".$table_name;
	$n = 0;
	$flds = '(';
	$vals = ') values (';
	foreach ($fields as $field) {
		$fieldname = $field['Field'];
		$fieldtype = $field['Type'];
		if ($fieldname == "id") {
			if (!empty($data['id']) && $data['id'] > 0) $vals .= $data['id'];
			else $vals .= 'null';
			$flds .= "id";
		}
		else if ($fieldname == "lastchange") {
			$vals .= ",now()";
			$flds .= ",".$fieldname;
		}
		else if (substr($fieldtype,0,7) == "varchar" )
		{
			if (!empty($data[$fieldname])) {	
			//$sql .= $fieldname."='".mysql_escape_string( $user[$fieldname])."'";
				$vals .= ",'".mysql_escape_string( $data[$fieldname])."'";
				$flds .= ",".$fieldname;
			}
		}
		else if ($fieldtype == "tinyint(1)") {
			if (!empty($data[$fieldname])) {	
				if (empty($data[$fieldname]) || $data[$fieldname] != 1) {
					$vals .= ",false";
					// $sql .= $fieldname."=false";
				}
				else {
					$vals .= ",true";
					// $sql .= $fieldname."=true";
				}
				$flds .= ",".$fieldname;
			}
	
		}
		else if (substr($fieldtype,0,3) == "int" )
		{
			if (!empty($data[$fieldname])) {
			//$sql .= $fieldname."='".mysql_escape_string( $user[$fieldname])."'";
				$vals .= ",".mysql_escape_string( $data[$fieldname]);
				$flds .= ",".$fieldname;
			}
		}
		else {
		// 	echo "unknown fieldtype ".$fieldtype;
		}
	}
	$sql .= $flds.$vals.')';
	test_mysql_query($sql);
	return $sql;
}

function build_update_sql($table_name,$fields,$data)
{

	$sql = "update ".$table_name." set ";
	$n = 0;
	$delim = '';
	foreach ($fields as $field) {
		$fieldname = $field['Field'];
		$fieldtype = $field['Type'];
		if ($fieldname == "id") {
			// ignore
		}
		if ($fieldname == "lastchange") {
			$sql .= ",lastchange= now()";
		}
		else if (substr($fieldtype,0,7) == "varchar" )
		{
			if (isset($data[$fieldname])) {
				$sql .= $delim.$fieldname."='".mysql_escape_string( $data[$fieldname])."'";
				
			}
		}
		
		else if (substr($fieldtype,0,3) == "int" || $fieldtype == "tinyint(1)")
		{
			if (isset($data[$fieldname])) {
				if ($data[$fieldname] != '')
					$sql .= $delim.$fieldname."=".mysql_escape_string( $data[$fieldname])."";
					else
						$sql .= $delim.$fieldname."=0";
				//$sql .= $fieldname."='".mysql_escape_string( $user[$fieldname])."'";
				// $sql .= $delim.$fieldname."=".mysql_escape_string( $data[$fieldname]);
				
			}
		}
		else {
			// if (isset($data[$fieldname])) {
			if (array_key_exists($fieldname,$data)) {
				if (isset($data[$fieldname]))
					$sql .= $delim.$fieldname."='".mysql_escape_string( $data[$fieldname])."'";
				else 
					$sql .= $delim.$fieldname."=null";
			
			}
			// 	echo "unknown fieldtype ".$fieldtype;
		}
		$delim = ', ';
	}

	$sql .= " where id=".$data['id'];
		
	test_mysql_query($sql);
	return $sql;
}
// $sql .= " where ID=".$user['id'];

// 


function show_fields($fieldnames)
{
	
	echo json_encode($fieldnames);
}
?>