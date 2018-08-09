<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];
echo "new_comp.php";
// $pt is an array of plating teams
$pt = json_decode($_POST["data"],true);
$i = 0;
$sql = "select * from plating_team_member where DATE(time_added) = CURDATE()";
$result = mysql_query($sql);

$existing= array();
while ($row = mysql_fetch_array($result)) {
	$existing[] = $row;
}

 
 foreach ($pt as $p) {
	
	if ($p != null ) {
		echo "$i---\n";
		echo $p[0]['email'];
		//var_dump($p[0]['email']);
		// echo "XXXXXXXXXXXXXXXXX\n\n";
		foreach ($p as $j) {
			$user_id = $j['id'];
			if (!check_if_exists($existing,$i,$user_id)) {
				$sql = "insert into plating_team_member (id,team_id,user_id,time_added) values (null,";
				$sql .= $i .",".$user_id.",now())";
				echo $sql."\n\n";
				test_mysql_query($sql);
			}
			// echo $j['id']."\n";
		}
	}
	$i++;
}


function check_if_exists($existing,$team_id,$user_id)
{
	foreach ($existing as $row) {
		// var_dump($row);
		if ($row['user_id'] == $user_id && $row['team_id'] == $team_id) {
			return(true);
		}
	}
	return(false);
}
?>