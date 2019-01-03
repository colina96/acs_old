<?php
session_start();

include '../db.php';

$job_dir = "/tmp/monarch/jobs/";
$job_dir = "tmp/";
$params = get_params();

if (!empty($_POST['data'])) {
	$comp = json_decode($_POST["data"],true);
	$id = $comp['id'];
	$d_copies = $comp['description_labels'];
	$t_copies = $comp['trolley_labels'];
	$dish_name = $comp['dish_name'];
	if (strlen($dish_name) > 25) {
		$dish_name = substr($dish_name,0,25);
	}
	$expiry_date = $comp['expiry_date'];
	$code = $comp['code'];
	
	$tmp_file = $job_dir.'trolley'.$id.".tmp";
	$job_file = $job_dir.'trolley'.$id.".job";
	echo "opening ".$tmp_file;
	$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);

	echo "Sending to printer KITCHEN_LABEL".$params['KITCHEN_LABELS_IP'].":".$params['KITCHEN_LABELS_PORT']."\n";
	fwrite($handle,"Jobname:trolley ".$id."\n");
	fwrite($handle,"Printer:".$params['KITCHEN_LABELS_IP']."\n");
	fwrite($handle,"Port:".$params['KITCHEN_LABELS_PORT']."\n");
	fwrite($handle,"Label:ACS_TROLLEY.LBL"."\n");
	fwrite($handle,"Endheader"."\n");
	fwrite($handle,"Copies:".$t_copies."\n");
	fwrite($handle,"NAME:".$dish_name."\n");
	fwrite($handle,"CODE:".$code."\n");
	$facility = 1; // not used yet.... maybe one day
	$barcode = sprintf("BARCODE:p%02d%06d",$facility,$id);
	fwrite($handle,$barcode."\n");
	//$barcodeTxt = sprintf("BARCODETXT:c%02d%06d",$facility,$id);
	//fwrite($handle,$barcodeTxt."\n");
	$d = strtotime($expiry_date);
	$barcodeTxt = "EXPIRYDATE:".date("d M y H:i",$d); 
	fwrite($handle,$barcodeTxt."\n"); 
	// $d = strtotime($prepped_date);
	//$barcodeTxt = "PREPPED:".date("d M y H:i",$d);
	// fwrite($handle,$barcodeTxt."\n");
	fwrite($handle,"Endlabel"."\n");
	fclose($handle);
	chmod ($tmp_file,0666);
	rename($tmp_file,$job_file);
	// now do plating label
	$tmp_file = $job_dir.'plate'.$id.".tmp";
	$job_file = $job_dir.'plate'.$id.".job";
	echo "opening ".$tmp_file;
	$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);
	
	fwrite($handle,"Jobname:plating ".$id."\n");
	fwrite($handle,"Printer:".$params['KITCHEN_LABELS_IP']."\n");
	fwrite($handle,"Port:".$params['KITCHEN_LABELS_PORT']."\n");
	fwrite($handle,"Label:ACS_PL.LBL"."\n");
	fwrite($handle,"Endheader"."\n");
	fwrite($handle,"Copies:".$d_copies."\n");
	fwrite($handle,"NAME:".$dish_name."\n");
	// fwrite($handle,"CODE:".$code."\n");
	$facility = 1; // not used yet.... maybe one day
	$barcode = sprintf("BARCODE:p%02d%06d",$facility,$id);
	fwrite($handle,$barcode."\n");
	//$barcodeTxt = sprintf("BARCODETXT:c%02d%06d",$facility,$id);
	//fwrite($handle,$barcodeTxt."\n");
//	$d = strtotime($expiry_date);
////	$barcodeTxt = "EXPIRYDATE:".date("d M y H:i",$d);
//	fwrite($handle,$barcodeTxt."\n");
	// $d = strtotime($prepped_date);
	//$barcodeTxt = "PREPPED:".date("d M y H:i",$d);
	// fwrite($handle,$barcodeTxt."\n");
	fwrite($handle,"Endlabel"."\n");
	fclose($handle);
	chmod ($tmp_file,0666);
	rename($tmp_file,$job_file);
}
?>
