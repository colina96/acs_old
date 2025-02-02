<?php
session_start();

include '../db.php';
include 'rest_common.php';
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
$params = get_params();

if (!empty($_POST['data'])) {
	$qa = get_qa();
	$comp = json_decode($_POST["data"],true);
	$id = $comp['id'];
	$dock = empty($comp['dock'])?false:$comp['dock'];
	$copies = $comp['copies'];
	if ($copies <= 0) exit;
	$description = $comp['description'];
	$desc2 = '';
	if (strlen($description) > 25) {
		$bestpos = 35;
		echo 'description too long for one line '.strlen($description);
		for ($i = 20; $i < strlen($description); $i++) {
			$s = substr($description,$i,1);
			echo $s;
			if ($s == ' ' && $i < 35) {
				echo 'found space at '.$i;
				$bestpos = $i;
				
				echo '2nd line'.$desc2;
				// break;
			}
		}
		$desc2 = substr($description,$bestpos + 1);
		$description = substr($description,0,$bestpos);
		
	}
	$expiry_date = $comp['expiry_date'];
	$prepped_date = $comp['M1_time'];
	$action_code = '';
	if (!empty($comp['M1_action_code'])) {
		$M1_action_code = $comp['M1_action_code'];
		$action_code = (!empty($qa[$M1_action_code]))?$qa[$M1_action_code]['action_text']:'-';
	}
	
	$preparedBy = $comp['preparedBy'];
	$tmp_file = $job_dir.'comp'.$id.".tmp";
	$job_file = $job_dir.'comp'.$id.".job";

	echo "opening ".$tmp_file."\n";
	$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);
	echo "opened ".$tmp_file;

	if ($dock) {
		echo "Sending to printer DOCK_LABEL".$params['DOCK_LABELS_IP'].":".$params['DOCK_LABELS_PORT']."\n";
		fwrite($handle,"Jobname:user ".$id."\n");
		fwrite($handle,"Printer:".$params['DOCK_LABELS_IP']."\n");
		fwrite($handle,"Port:".$params['DOCK_LABELS_PORT']."\n");
		fwrite($handle,"Label:ACS_DOCK.LBL"."\n");
	}
	else {
		echo "Sending to printer KITCHEN_LABEL".$params['KITCHEN_LABELS_IP'].":".$params['KITCHEN_LABELS_PORT']."\n";
		fwrite($handle,"Jobname:user ".$id."\n");
		fwrite($handle,"Printer:".$params['KITCHEN_LABELS_IP']."\n");
		fwrite($handle,"Port:".$params['KITCHEN_LABELS_PORT']."\n");
		fwrite($handle,"Label:ACS_COMP.LBL"."\n");
	}
	
	fwrite($handle,"Endheader"."\n");
	fwrite($handle,"Copies:".$copies."\n");
	fwrite($handle,"NAME:".$description."\n");
	fwrite($handle,"NAME2:".$desc2."\n");
	if (!$dock) 
		fwrite($handle,"PREPAREDBY:".$preparedBy."\n");
	else
		fwrite($handle,"Received by:".$preparedBy."\n");
	
	
	$facility = 1; //TODO not used yet.... maybe one day
	$barcode = sprintf("BARCODE:c%02d%06d",$facility,$id);
	fwrite($handle,$barcode."\n");
	//$barcodeTxt = sprintf("BARCODETXT:c%02d%06d",$facility,$id);
	//fwrite($handle,$barcodeTxt."\n");
	$d = strtotime($expiry_date);
	if ($d == 0) 
		$barcodeTxt = "EXPIRYDATE:".$expiry_date;
	else	
		$barcodeTxt = "EXPIRYDATE:".date("d M y H:i",$d);
	fwrite($handle,$barcodeTxt."\n");
	$d = strtotime($prepped_date);
	if (!$dock) 
	
		$barcodeTxt = "PREPPED:".date("d M y H:i",$d);
	else
		$barcodeTxt = "RECEIVED:".date("d M y H:i",$d);
	fwrite($handle,$barcodeTxt."\n");
	if ($dock) {
		fwrite($handle,"M1_ACTION:".$action_code."\n");
	}
	fwrite($handle,"Endlabel"."\n");
	fclose($handle);
	chmod ($tmp_file,0666);
	rename($tmp_file,$job_file);
}
?>
