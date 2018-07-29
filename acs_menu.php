<?php include 'acs_head.php' ?>
<?php 
function upload_menu()
{
if(isset($_POST["submit_menu"])) {
//	echo "menu name ".$_POST['menu_description']."<br>\n";
//	echo "comment ".$_POST['menu_comment']."<br>\n";
//	echo "uploaded ".$_FILES["fileToUpload"]["name"]."\n";
//	echo "tmp name ".$_FILES["fileToUpload"]["tmp_name"]."\n";
//	echo "start ".$_POST['menu_start']."<br>\n";
//	echo "end ".$_POST['menu_end']."<br>\n";
// $file = 'menu.csv';
	$file = $_FILES["fileToUpload"]["tmp_name"];

    $csv = array_map('str_getcsv', file($file));
//	echo "read ".sizeof($csv)." rows\n";
	
//	echo "read ".sizeof($csv[0])."columns\n";
	// $menu_id = new_menu("TEST MENU","description","comment");
	$sql = "insert into MENUS ";
	$flds = "(id,start_date,end_date,description,code,comment)";
	
	$vals = " values (null,'".$_POST['menu_start']."','".$_POST['menu_end']."',";
	$vals .= "'".mysql_escape_string( $_POST['menu_name'])."',";
	$vals .= "'".mysql_escape_string( $_POST['menu_description'])."',";
	$vals .= "'".mysql_escape_string($_POST['menu_comment'])."')";
	$sql .= $flds.$vals;
//	echo $sql;
	test_mysql_query($sql);
	$menu_id = mysql_insert_id();
	$menu_item_id = -1;
	//echo "<table>\n";
	$ignore_next = false;
	foreach ($csv as $row) 
	{
	//	echo "<tr>\n";
		if (sizeof ($row[0]) > 0) {
			$field = $row[0];
			if (is_string($field) && strlen($field) > 0) {
				if( $field[0] == 'F') {
					$ignore_next = false;
				//	echo "<td>Menu item ".$row[0]." ".$row[2]."\n";
					$sql = "insert into MENU_ITEMS ";
					$flds = "(id,menu_id,code,dish_name)";
					$vals = " values (null,".$menu_id.",";
					$vals .= "'".mysql_escape_string( $row[0])."',";
					$vals .= "'".mysql_escape_string($row[2])."')";
					$sql .= $flds.$vals;
					// echo $sql;
					test_mysql_query($sql);
					
					$menu_item_id = mysql_insert_id();
				}
				else {
					if ($row[2] == '') {
						$ignore_next = true;
					}
					if (!$ignore_next) {
					//	echo "<td></td><td>$ignore_next Component $row[2]\n";
						$sql = "insert into MENU_ITEM_COMPONENTS ";
						$flds = "(id,menu_item_id,description)";
						$vals = " values (null,".$menu_item_id.",";
						$vals .= "'".mysql_escape_string( $row[2])."')";
						$sql .= $flds.$vals;
					//	echo $sql;
						test_mysql_query($sql);
						// return(mysql_insert_id());
		//				add_menu_component($menu_item_id,$row[2],0);
					}
				}
			}
		}
	}

	
}
}
?>
<script>
var default_tab = 'MENU'; 
</script> 
<?php include 'acs_footer.php' ?>
