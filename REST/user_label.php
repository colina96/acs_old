<?php
session_start();

include '../db.php';
/*

Print user nametag label thing.
Format...

Jobname:prep 1
Printer:10.0.0.100
Port:9101
Label:USER.LNT
Endheader
# start of individual labels
Copies:1
NAME:David Cox
#barcode should be no more than many digits
BARCODE:U01000002
Endlabel
*/
/* need some security - make sure requester is logged in

$userID = $_SESSION['userID'];

*/
$uid =null;
$username = null;
$job_dir = "/tmp/monarch/jobs/";
$job_dir = "tmp/";
$params = get_params();

if (!empty(get_url_token('username')) && !empty(get_url_token('uid'))) {
	$username = get_url_token('username');
	$uid = get_url_token('uid');
}
else if (!empty($_POST['data'])) {
	$user = json_decode($_POST["data"],true);
	$uid = $user['id'];
	$username = $user['firstname'].' '.$user['lastname'];
}
if ($username != null && $uid != null) {
	$tmp_file = $job_dir.'user'.$uid.".tmp";
	$job_file = $job_dir.'user'.$uid.".job";
	echo "openning ".$tmp_file;
	$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);

	echo "opened ".$tmp_file;
	fwrite($handle,"Jobname:user label ".$uid."\n");
	fwrite($handle,"Printer:".$params['KITCHEN_BELS_IP']."\n");
	fwrite($handle,"Port:9100".$params['KITCHEN_LABELS_PORT']."\n");
	fwrite($handle,"Label:ACS_USER.LBL"."\n");
	fwrite($handle,"Endheader"."\n");
	fwrite($handle,"Copies:1"."\n");
	fwrite($handle,"NAME:".$username."\n");
	$facility = 1; // not used yet.... maybe one day
	$barcode = sprintf("BARCODE:u%02d%06d",$facility,$uid);
	// fwrite($handle,"BARCODE:U01000002"."\n");
	fwrite($handle,$barcode."\n");
	fwrite($handle,"Endlabel"."\n");
	fclose($handle);
	chmod ($tmp_file,0666);
	rename($tmp_file,$job_file);
}
?>
