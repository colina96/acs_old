<?php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	echo "menu name ".$_POST['description']."<br>\n";
	echo "uploaded ".$_FILES["fileToUpload"]["name"]."\n";
	echo "tmp name ".$_FILES["fileToUpload"]["tmp_name"]."\n";
// $file = 'menu.csv';
	$file = $_FILES["fileToUpload"]["tmp_name"];

    $csv = array_map('str_getcsv', file($file));
	echo "read ".sizeof($csv)." rows\n";
	
	echo "read ".sizeof($csv[0])."columns\n";
	// $menu_id = new_menu("TEST MENU","description","comment");
	$menu_item_id = -1;
	echo "<table>\n";
	$ignore_next = false;
	foreach ($csv as $row) 
	{
		echo "<tr>\n";
		if (sizeof ($row[0]) > 0) {
			$field = $row[0];
			if (is_string($field) && strlen($field) > 0) {
				if( $field[0] == 'F') {
					$ignore_next = false;
					echo "<td>Menu item ".$row[0]." ".$row[2]."\n";
	//				$menu_item_id = new_menu_item($menu_id,$row[0],$row[2]);
				}
				else {
					if ($row[2] == '') {
						$ignore_next = true;
					}
					if (!$ignore_next) {
						echo "<td></td><td>$ignore_next Component $row[2]\n";
		//				add_menu_component($menu_item_id,$row[2],0);
					}
				}
			}
		}
	}

	echo "</table>";
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
?>
