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
$job_dir = "/tmp/monarch/jobs/";
$job_dir = "tmp/";

if (!empty($_POST['data'])) {
	$comp = json_decode($_POST["data"],true);
	$id = $comp['id'];
	$copies = $comp['copies'];
	if ($copies <= 0) exit;
	$description = $comp['description'];
	if (strlen($description) > 25) {
		$description = substr($description,0,25);
	}
	$expiry_date = $comp['expiry_date'];
	$prepped_date = $comp['M1_time'];
	$preparedBy = $comp['preparedBy'];
	$tmp_file = $job_dir.'comp'.$id.".tmp";
	$job_file = $job_dir.'comp'.$id.".job";
	echo "openning ".$tmp_file;
	$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);

	echo "opened ".$tmp_file;
	fwrite($handle,"Jobname:user ".$id."\n");
	fwrite($handle,"Printer:".$params['KITCHEN_LABELS_IP']."\n");
	fwrite($handle,"Port:".$params['KITCHEN_LABELS_PORT']."\n");
	fwrite($handle,"Label:ACS_COMP.LBL"."\n");
	fwrite($handle,"Endheader"."\n");
	fwrite($handle,"Copies:".$copies."\n");
	fwrite($handle,"NAME:".$description."\n");
	fwrite($handle,"PREPAREDBY:".$preparedBy."\n");
	$facility = 1; // not used yet.... maybe one day
	$barcode = sprintf("BARCODE:c%02d%06d",$facility,$id);
	fwrite($handle,$barcode."\n");
	//$barcodeTxt = sprintf("BARCODETXT:c%02d%06d",$facility,$id);
	//fwrite($handle,$barcodeTxt."\n");
	$d = strtotime($expiry_date);
	$barcodeTxt = "EXPIRYDATE:".date("d M y H:i",$d);
	fwrite($handle,$barcodeTxt."\n");
	$d = strtotime($prepped_date);
	$barcodeTxt = "PREPPED:".date("d M y H:i",$d);
	fwrite($handle,$barcodeTxt."\n");
	fwrite($handle,"Endlabel"."\n");
	fclose($handle);
	chmod ($tmp_file,0666);
	rename($tmp_file,$job_file);
}
?>
