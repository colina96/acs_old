<?php
session_start();

include '../db.php';

$job_dir = "/tmp/monarch/jobs/";
$job_dir = "tmp/";
$params = get_params();

if (!empty($_POST['data'])) {
	
	$comp = json_decode($_POST["data"],true);
	var_dump($comp);
	$id = $comp['id'];
	$d_copies = $comp['description_labels'];

	$dish_name = $comp['dish_name'];
	if (strlen($dish_name) > 25) {
		$dish_name = substr($dish_name,0,25);
	}
	$expiry_date = $comp['expiry_date'];
	$code = $comp['code'];
	if ($comp['serves'] && $comp['serves']['ss1']) {
		$serves = array();
		if (!empty($comp['serves']['ss1']) && !empty($comp['serves']['ss1_no'] )) {
			$serve = array();
			$serve['servesize'] = $comp['serves']['ss1'];
			$serve['n'] = $comp['serves']['ss1_no'];
			$serves[] = $serve;
		}
		if (!empty($comp['serves']['ss2']) && !empty($comp['serves']['ss2_no'] )) {
			$serve = array();
			$serve['servesize'] = $comp['serves']['ss2'];
			$serve['n'] = $comp['serves']['ss2_no'];
			$serves[] = $serve;
		}
		if (!empty($comp['serves']['units'])  ) {
			$serve = array();
			$serve['servesize'] = 1;
			$serve['n'] = $comp['serves']['units'];
			$serves[] = $serve;
		}
		foreach ($serves as $serve) {
			$tmp_file = $job_dir.'plate'.$id.'_'.$serve['servesize'].".tmp";
			$job_file = $job_dir.'plate'.$id.'_'.$serve['servesize'].".job";
			echo "opening ".$tmp_file;
			$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);
			$units = ' UNITS';
			if ($serve['servesize'] == 1) $units = ' UNIT';
			fwrite($handle,"Jobname:plating ".$id."\n");
			fwrite($handle,"Printer:".$params['PLATING_LABELS1_IP']."\n");
			fwrite($handle,"Port:".$params['PLATING_LABELS1_PORT']."\n");
			fwrite($handle,"Label:ACS_PL_SERVE.LBL"."\n");
			fwrite($handle,"Endheader"."\n");
			// fwrite($handle,"Copies:".$serve['n']."\n");
			fwrite($handle,"Copies:".'1'."\n");
			fwrite($handle,"SERVESIZE:".$serve['servesize'].$units."\n");
			$d = strtotime($comp['expiry_date']);
			$expiryTxt = "USE BY:".date("d M y H:i",$d);
			fwrite($handle,"EXPIRY:".$expiryTxt."\n");
			fwrite($handle,"NAME:".$dish_name."\n");
			fwrite($handle,"CODE:".$code."\n");
			$facility = 1; // not used yet.... maybe one day
			$barcode = sprintf("BARCODE:p%02d%06d",$facility,$id);
			fwrite($handle,$barcode."\n");
			
			fwrite($handle,"Endlabel"."\n");
			fclose($handle);
			chmod ($tmp_file,0666);
			rename($tmp_file,$job_file);
		}
	}
	else {
		$tmp_file = $job_dir.'plate'.$id.".tmp";
		$job_file = $job_dir.'plate'.$id.".job";
		echo "opening ".$tmp_file;
		$handle = fopen($tmp_file, 'w') or die('Cannot open file:  '.$tmp_file);
		
		fwrite($handle,"Jobname:plating ".$id."\n");
		fwrite($handle,"Printer:".$params['PLATING_LABELS1_IP']."\n");
		fwrite($handle,"Port:".$params['PLATING_LABELS1_PORT']."\n");
		fwrite($handle,"Label:ACS_PL.LBL"."\n");
		fwrite($handle,"Endheader"."\n");
		fwrite($handle,"Copies:".$d_copies."\n");
		fwrite($handle,"NAME:".$dish_name."\n");
		fwrite($handle,"CODE:".$code."\n");
		$facility = 1; // not used yet.... maybe one day
		$barcode = sprintf("BARCODE:p%02d%06d",$facility,$id);
		fwrite($handle,$barcode."\n");
		
		fwrite($handle,"Endlabel"."\n");
		fclose($handle);
		chmod ($tmp_file,0666);
		rename($tmp_file,$job_file);
	}
	
}


?>
