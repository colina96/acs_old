<?php include '../db.php' ?>
<?php
$file = 'menu.csv';

    $csv = array_map('str_getcsv', file($file));
	echo "read ".sizeof($csv)." rows\n";
	
	echo "read ".sizeof($csv[0])."columns\n";
	$menu_id = new_menu("TEST MENU","description","comment");
	$menu_item_id = -1;
	foreach ($csv as $row) 
	{
		if (sizeof ($row[0]) > 0) {
			$field = $row[0];
			if (is_string($field) && strlen($field) > 0) {
				if( $field[0] == 'F') {
					echo "Menu item ".$row[0]." ".$row[2]."\n";
					$menu_item_id = new_menu_item($menu_id,$row[0],$row[2]);
				}
				else {
					echo "Component $row[2]\n";
					add_menu_component($menu_item_id,$row[2],0);
				}
			}
		}
	}

function new_menu($menu_name,$description,$comment)
{
	$sql = "insert into MENUS ";
	$flds = "(id,start_date,end_date,description,code,comment)";
	
	$vals = " values (null,now(),now(),";
	$vals .= "'".mysql_escape_string( $menu_name)."',";
	$vals .= "'".mysql_escape_string( $description)."',";
	$vals .= "'".mysql_escape_string($comment)."')";
	$sql .= $flds.$vals;
	echo $sql;
	test_mysql_query($sql);
	return(mysql_insert_id());
}

function new_menu_item($menu_id,$menu_item_code,$menu_item_dish_name)
{
	$sql = "insert into MENU_ITEMS ";
	$flds = "(id,menu_id,code,dish_name)";
	$vals = " values (null,".$menu_id.",";
	$vals .= "'".mysql_escape_string( $menu_item_code)."',";
	$vals .= "'".mysql_escape_string($menu_item_dish_name)."')";
	$sql .= $flds.$vals;
	echo $sql;
	test_mysql_query($sql);
	return(mysql_insert_id());
}
	
function add_menu_component($menu_item_id,$description,$prep_type)
{
		

	$sql = "insert into MENU_ITEM_COMPONENTS ";
	$flds = "(id,menu_item_id,description,prep_type)";
	$vals = " values (null,".$menu_item_id.",";
	$vals .= "'".mysql_escape_string( $description)."',".$prep_type.")";
	$sql .= $flds.$vals;
	echo $sql;
	test_mysql_query($sql);
	return(mysql_insert_id());
}
?>

