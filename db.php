<?php

	$hostname = '127.0.0.1';
	$dbuser = 'acs';
	$dbpw = 'acs'; 
	$dbname = "acs";
	$con = @mysqli_connect($hostname,$dbuser, $dbpw,$dbname); #pconnect is php pooled connections
	$GLOBALS['con'] = $con;
	$logout = get_url_token("logout");
	
	if (!empty($logout)) check_login();
	if (empty($_SESSION['userID'])) { 
		check_login();
	}
	else {
		// echo "<!-- user: ".$_SESSION['userID']." ".$_SESSION['user']. "-->";
	}

function add_var($varname)
{
	if (empty($_SESSION[$varname])) {
		echo "var ".$varname. " = -1;\n";
	}
	else {
		echo "var ".$varname. " = ".$_SESSION[$varname].";\n";
	}
}

function get_url_token($tok)
{
	if (isset($_POST[$tok])) {
		return($_POST[$tok]);
	}
	if (isset($_GET[$tok])) {
		return($_GET[$tok]);
	}
	return(NULL);
}

function mysql_query($sql) { return(mysqli_query($GLOBALS['con'],$sql)); }
function mysql_errno() { return(mysqli_errno($GLOBALS['con'])); }
function mysql_error() { return(mysqli_error($GLOBALS['con'])); }
function mysql_insert_id() { return(mysqli_insert_id ( $GLOBALS['con'] )); }

function mysql_escape_string($s) { return (mysqli_escape_string($GLOBALS['con'],$s)); }
function mysql_fetch_array($s) { return (mysqli_fetch_array($s)); }

function get_fieldnames($table_name)
{
	$fieldnames = array();
	$result = mysql_query("show columns from ".$table_name);
	while ($row = mysql_fetch_array($result)) {
		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
	}
	return($fieldnames);
}

function get_table($tablename,$conditions)
{
	$fieldnames = get_fieldnames($tablename);
	$sql = "select * from ".$tablename;
	if (!empty($conditions)) {
		$sql .= ' '.$conditions;
	}
	$result = mysql_query($sql);
	$comps = array();
	if ($result) {
	
		while($row = mysql_fetch_array($result))
		{
			$comp = array();
			foreach ($fieldnames as $f) {
				$comp[$f] = utf8_encode($row[$f]);
			}
			$comps[$row['id']] = $comp;
		}
	}
	return($comps);
}

function test_mysql_query($sql)
{
	$result = mysql_query($sql);
	if (mysql_errno() != 0) {
	//	echo(mysql_error());
		echo("<br>ERROR with ".$sql."<br><hr>");
		// var_dump(debug_backtrace());
	}
	return($result);
}

function check_login()
{
	$email = get_url_token("email");
 	//  echo "checking login";
	$password = get_url_token("password");
	$uid = get_url_token("uid");
	$login = get_url_token("login");
	$logout = get_url_token("logout");
	$found = 0;
	if (!empty($logout) && empty($login)) {
		$_SESSION['user'] = NULL;
		$_SESSION['userID'] = NULL;
		$_SESSION['admin'] = NULL;
		$_SESSION['orderID'] = NULL;
		$_SESSION['email'] = NULL;
		$_SESSION['familyname'] = NULL;
		$_SESSION['firstname'] = NULL;
		$_SESSION['orderID'] = NULL;
	}
	else if (!empty($login)) {
		
		$sql = NULL;
		if (!empty($email) && !empty($password)) {
			$sql="SELECT * FROM USERS where email='" . $email."' and password = '".$password."'" ;
		}
		if (!empty($uid)) {
			$sql="SELECT * FROM USERS where id=" . $uid;
		}
		if ($sql) {
	
			// echo $sql;
			$result = mysql_query($sql);
			if ($result) {
				while($row = mysql_fetch_array($result))
				{
					$found++;
					$_SESSION['user'] = $row['firstname']." ".$row['lastname'];
					$_SESSION['userID'] = $row['id'];
					$_SESSION['admin'] = $row['admin'];
					$_SESSION['email'] = $row['email'];
					$_SESSION['lastname'] = $row['lastname'];
					$_SESSION['firstname'] = $row['firstname'];
				}
			}
			else {
				echo "no result for ".$sql;
			}
		}
		if ($found == 0) {
		//	echo "not found<br>";
			$_SESSION['user'] = NULL;
			$_SESSION['userID'] = NULL;
			$_SESSION['admin'] = NULL;
			$_SESSION['orderID'] = NULL;
			$_SESSION['email'] = NULL;
			$_SESSION['familyname'] = NULL;
			$_SESSION['firstname'] = NULL;
			$_SESSION['orderID'] = NULL;
		}
		else {
			$sql = "update USERS set last_login=now() where ID=".$_SESSION['userID'];
			$result = mysql_query($sql);
			if (mysql_errno() != 0) {
				echo(mysql_error());
				echo("<br>".$sql);
			}
	
		}
	
	}
}


