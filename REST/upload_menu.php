<?php
session_start();

include '../db.php';
$con = $GLOBALS['con'];

upload_menu();

function upload_menu()
{
	if (!isset($_FILES["fileToUpload"]) || 
			!isset($_POST['menu_name']) ||
			!isset($_POST['menu_description'])) {
		echo "not complete data ".$_POST['menu_name'];
		return;
	}
	ini_set("auto_detect_line_endings", "1");
	$file = $_FILES["fileToUpload"]["tmp_name"];
	echo "uploading ".$_FILES["fileToUpload"]["name"]."\n";
	echo "tmp name ".$_FILES["fileToUpload"]["tmp_name"]."\n";
	echo "start ".$_POST['menu_start']."<br>\n";
	echo "end ".$_POST['menu_end']."<br>\n";
	if(/* isset($_POST["submit_menu"]) &&  */ strlen($file) > 3) {
		//	echo "menu name ".$_POST['menu_description']."<br>\n";
		//	echo "comment ".$_POST['menu_comment']."<br>\n";
		//	echo "uploaded ".$_FILES["fileToUpload"]["name"]."\n";
		//	echo "tmp name ".$_FILES["fileToUpload"]["tmp_name"]."\n";
		//	echo "start ".$_POST['menu_start']."<br>\n";
		//	echo "end ".$_POST['menu_end']."<br>\n";
		// $file = 'menu.csv';
		$file = $_FILES["fileToUpload"]["tmp_name"];
	//	echo "file |".$file.'|';
		$csv = array_map('str_getcsv', file($file));
		echo "read ".sizeof($csv)." rows\n";

		echo "read ".sizeof($csv[0])."columns\n";
		// $menu_id = new_menu("TEST MENU","description","comment");
		$sql = "insert into MENUS ";
		$flds = "(id,start_date,end_date,description,code,comment)";

		$vals = " values (null,'".$_POST['menu_start']."','".$_POST['menu_end']."',";
		$vals .= "'".mysql_escape_string( $_POST['menu_name'])."',";
		$vals .= "'".mysql_escape_string( $_POST['menu_description'])."',";
		$vals .= "'".mysql_escape_string($_POST['menu_comment'])."')";
		$sql .= $flds.$vals;
//		echo $sql;
		test_mysql_query($sql);
		$menu_id = mysql_insert_id();
		$menu_item_id = -1;
		//echo "<table>\n";
		$ignore_next = false;
		$last_component = -1;
		foreach ($csv as $row)
		{
	//		echo "got row";
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
						$last_component = -1;
					}
					else {
						if ($row[2] == '' && $row[3] == '') {
							$ignore_next = true;
						}
						$supplier = mysql_escape_string( $row[4]);
						$product = mysql_escape_string( $row[5]);
						$spec = mysql_escape_string( $row[6]);
						$shelflife = $row[7];
						$preptype_str = $row[8];
						$preptype = 0;
						// horrible hack - should get values from db ... TODO
						if ($preptype_str == 'FRESH') $preptype = 6;
						if ($preptype_str == 'FROZEN') $preptype = 7;
						if ($preptype_str == 'DRY') $preptype = 8;
						if (!$ignore_next && $row[2] != '') {
							// search for component - only create new if not in already there
							$description = mysql_escape_string( $row[2]);
						//	echo "found $description";
							$comp_id = get_component_id($description,$menu_id,null,'','',0,null,0);
							$last_component = $comp_id;
							$sql = "insert into MENU_ITEM_LINK (id,menu_id,menu_item_id,component_id) ";
							$sql .= "values (null,".$menu_id.",".$menu_item_id.",".$comp_id.")";
							test_mysql_query($sql);
							// return(mysql_insert_id());
							//				add_menu_component($menu_item_id,$row[2],0);
						}
						if ($row[3] != '' && $last_component >= 0) { // menu item high risk component 
							$description = mysql_escape_string( $row[3]);
							
							$subcomp_id = get_component_id($row[3],$menu_id,$supplier,$product,$spec,$shelflife,$preptype,1);
							$sql = "insert into COMPONENT_LINK (id,menu_id,component_id,subcomponent_id) ";
							$sql .= "values (null,".$menu_id.",".$last_component.",".$subcomp_id.")";
							test_mysql_query($sql);
						}
					}
				}
			}
		}


	}
}
function get_component_id($description,$menu_id,$supplier,$product,$spec,$shelflife,$preptype,$track)
{
	$sql = "select * from MENU_ITEM_COMPONENTS where description='".$description."'"; // and menu_id=".$menu_id; 
	$result = mysql_query($sql);
	$comp_id = -1;
	while ($row = mysql_fetch_array($result) ) {
		$comp_id = $row['id'];
		// insert new component
	}
	if ($comp_id == -1) {
		$shelflife = intval($shelflife);
		$preptype = intval($preptype);
		$sql = "insert into MENU_ITEM_COMPONENTS ";
		$flds = "(id,menu_id,description,supplier,product,spec,shelf_life_days,high_risk,prep_type)";
		$vals = " values (null,".$menu_id;
		$vals .= ",'".$description."'";
		$vals .= ",'".$supplier."'";
		$vals .= ",'".$product."'";
		$vals .= ",'".$spec."'";
		$vals .= ",".$shelflife;
		$vals .= ",".$track;
		$vals .= ",".$preptype;
		$vals .= ")";
		$sql .= $flds.$vals;
		//	echo $sql;
		test_mysql_query($sql);
		$comp_id = mysql_insert_id();
	}
	return ($comp_id);
}
?>