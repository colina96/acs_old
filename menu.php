<div class='acs_main'>
	
		<div class="acs_sidebar">
		  <button type='button' class='acs_menu_btn' href="#" id="add_new_menu"
		  onclick="openPage('new_menu', this, 'red','menu_details','acs_menu_btn')">Add new</button>
		  <div class='acs_menu_space' >SELECT A MENU</div>
		  <button type='button' class='acs_menu_btn' href="#" id="active_menu"  
		  	onclick="show_active_menus()">ACTIVE</button>
		  <button type='button' class='acs_menu_btn' href="#" id="future_menu" 
		  	onclick="openPage('future_menus', this, 'red','menu_details','acs_menu_btn')">FUTURE</button>
		  <button type='button' class='acs_menu_btn' href="#" id="expired_menu" 
		  	onclick="openPage('future_menus', this, 'red','menu_details','acs_menu_btn')">EXPIRED</button>
		  
		</div>
	
		<div class="acs_right_content">
			<div id='new_menu' class='menu_details'>
				
				<h1>CREATE NEW MENU</h1>
				<form method="post" enctype="multipart/form-data">
				<table><tr><td>OVERALL INFORMATION</td></tr>
				<tr><td><input name='menu_name'></td>
					<td><input name='menu_description'></td>
					<td><input name='menu_comment'></td>
				<tr><td>DATE RANGE</td></tr>
				<tr><td><input type="text" id="menu_start" name="menu_start" placeholder='start date' class='datepicker' readonly="readonly"></td>
				<td><input type="text" id="menu_end" name="menu_end" placeholder='end date' class='datepicker' readonly="readonly"></td></tr>
				<tr><td colspan='3'>
    			Select file to upload:
   				 <input type="file" name="fileToUpload" id="fileToUpload">
   				 <!--   div class='drop-files-container' id='drop-files-container'></div -->
   				 <input type='submit' name='submit_menu' value='Submit' class='submit'>
   				 </td></tr></table></form>
	
	
		
			</div>
			<div id='active_menus' class='menu_details menu_details_active'>Active menu details 
			<?php load_active_menus(); ?></div>
			<div id='future_menus' class='menu_details'>Future menu details
			<?php load_future_menus(); ?></div>
			<div id='expired_menus' class='menu_details'>Expired menu details
			<?php load_expired_menus(); ?></div>
			<div id='add_menu_component_modal'>
				<div class='modal_header'><span>Add menu item component</span>
				<div id='menu_item_name_div'></div>
				<div class='close_modal' onclick='close_menu_component_modal("add_menu_component_modal");'>X</div>
				</div>
			
				<div id='menu_item_component_div'>
				<form method='POST' action='acs_menu.php'>
				<input type='hidden' name='cc_menu_item_id' >
				<input type='hidden' name='cc_menu_id' >
				<input name='menu_item_component_description' size='30'>
				<select name='prep_type'>
				
<?php 
$sql = "select * from PREP_TYPES order by ID";
$result = mysql_query($sql);
if ($result) { 
	while($row = mysql_fetch_array($result)) {
		echo "<option value='".$row['id']."'>".$row['code']."</option>";
	}
}
?>
</select>
				<input type='submit' name='add_menu_component' value='Add'>
				</form></div>
			
		</div>
		<div id='del_menu_component_modal'>
			<div class='modal_header'>
				<span>Delete menu item component</span>
			
				<div class='close_modal' onclick='close_menu_component_modal("del_menu_component_modal");'>X</div>
			</div>
			<div id='menu_item_component_del_div'>
				<form method='POST'>
				<input type='hidden' name='cc_menu_item_component_id' >
				<input type='hidden' name='cc_menu_id' >
				<div id='menu_item_component_description'>----</div>
				<input type='submit' name='del_menu_component' value='Delete'>
				</form>
			</div>
		</div>
</div>
</div>
<script>
var menus = null;
function update_plating_team(s,id)
{
	
	// var s = document.getElementById("pt_" + comp_id);
	
	var idx = s.selectedIndex;
	var val = s.options[idx].value;
	console.log("update plating_team ",id,idx,val);
	
	$.post("REST/update_menu_item.php",
		    {
		        id: id,
		        plating_team: idx
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		    });
}


function update_probe_type(s,comp_id)
{
	
	// var s = document.getElementById("pt_" + comp_id);
	
	var idx = s.selectedIndex;
	var val = s.options[idx].value;
	console.log("update component id for ",comp_id,idx,val);
	var all = document.getElementsByName("probe_" + comp_id);
	console.log("found dups",all.length);
	for (i = 0; i < all.length; i++) {
		all[i].selectedIndex = idx;
	}
	$.post("REST/update_component.php",
		    {
		        id: comp_id,
		        probe_type: idx + 1
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		    });
}


function update_prep_type(s,comp_id)
{
	
	// var s = document.getElementById("pt_" + comp_id);
	
	var idx = s.selectedIndex;
	var val = s.options[idx].value;
	console.log("update component id for ",comp_id,idx,val);
	var all = document.getElementsByName("pt_" + comp_id);
	console.log("found dups",all.length);
	for (i = 0; i < all.length; i++) {
		all[i].selectedIndex = idx;
	}
	$.post("REST/update_component.php",
		    {
		        id: comp_id,
		        prep_type: idx + 1
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		    });
}

function show_active_menus()
{
	openPage('active_menus', this, 'red','menu_details','acs_menu_btn');
	$.ajax({
        url: "REST/get_menus.php",
        type: "POST",
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            menus = result;
           // document.getElementById('active_comps').innerHTML = result;
            console.log("got " + result.length + " menus");
            document.getElementById('active_menus').innerHTML = result;
            show_menus('active',result);
            
        },
        done: function(result) {
            console.log("done load_comps ");
        },
        fail: (function (result) {
            console.log("fail load_comps",result);
        })
    });
}

function processFileUpload(droppedFiles) 
{
    // add your files to the regular upload form
	var uploadFormData = new FormData($("#menuUploadForn")[0]); 
	if(droppedFiles.length > 0) { // checks if any files were dropped
		console.log("file dropped",droppedFiles.length);
		
  	 	for(var f = 0; f < droppedFiles.length; f++) { // for-loop for each file dropped
  	 		console.log("file dropped",droppedFiles[f]);
       		uploadFormData.append("fileToUpload",droppedFiles[f]);  // adding every file to the form so you could upload multiple files
  	 	}
   	}
}

function show_menus(active,data)
{
	var div = document.getElementById('active_menus');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Menus</h1>";
		return;
	}
	div.innerHTML = "<h1>Active Menus</h1>";
	var tab = document.createElement('table');
	tab.className = 'menu_table';
	var tr = document.createElement('tr');
	tr.appendChild(new_td('Description','comp'));
	tr.appendChild(new_td('Start','comp'));
    tr.appendChild(new_td('End','comp'));   
    
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		tr.appendChild(new_td(data[i]['description'],'comp'));
   		var start_date = new Date(data[i]['start_date']);
   		tr.appendChild(new_td(show_date(start_date),'comp'));
   		var end_date = new Date(data[i]['end_date']);
   		tr.appendChild(new_td(show_date(end_date),'comp'));
   		tr.appendChild(new_td("<a href='acs_menu.php?menu_id=" + data[i]['id'] + "'>edit</a>",'comp'));
   		// tr.appendChild(new_td("<button type='button' class='acs_comp_btn' onclick='act_component(" + i + ");'>Action</button>",'comp'));
   		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}

</script>
<!--   div class='new_menu'>
	<div class='new_menu_heading'><div class='new_menu_title'>CREATE A NEW MENU</div></div>
	<div class='new_menu_body'>
	</div>
</div -->
<?php 


function select_chef($s_name)
{
	
	echo "<select name='$s_name'>";

	$sql = "select * from USERS where function='chef' order by ID";
	$result = mysql_query($sql);
	if ($result) {
		while($row = mysql_fetch_array($result)) {
			echo "<option value='".$row['id']."'>".$row['firstname'].' '.$row['lastname']."</option>";
		}
	}

	echo "</select>";
}




function load_active_menus()
{
	load_menus();
}
function load_future_menus()
{
	echo "active menus";
}
function load_expired_menus()
{
	echo "active menus";
}
function load_menus()
{
	// echo "load menus";
	if( !empty($_SESSION['userID'])) { // logged in
		if (!empty($_POST['delete_menu'])) { delete_menu(); }
		if (!empty($_POST['update_menu'])) { update_menu(); }
		if (!empty($_POST['new_menu_item'])) { new_menu_item(); }
		if (!empty($_POST['add_menu_component'])) { add_menu_component(); }
		if (!empty($_POST['del_menu_component'])) { del_menu_component(); }
		$menu_id = get_url_token('menu_id'); //  || get_url_token('cc_menu_id');
		// echo "menu id $menu_id   ";
		if (!empty($menu_id)) {		
			//echo "<div class='draw_screen_btn'><a class='users_link' href='acs_menu.php'>Back</a></div>";
			show_menu($menu_id);
			//echo "<div class='draw_screen_btn'><a class='users_link' href='acs_menu.php'>Back</a></div>";
		}
		else {
			show_menus();
			
		}
	}
}

function show_menus()
{
	$sql = "select * from MENUS order by ID";
	$result = mysql_query($sql);
	if ($result) {

		echo "<table class='menus_table' width='100%' border='1'>";
		echo "<tr><td>Description</td><td>Start Date</td><td>End Date</td><td>Code</td>";
		
		while($row = mysql_fetch_array($result))
		{
//			echo "<tr class='users'><td><a href='?edit=".$row['id']."'>".$row['id']."</a></td>";
//			echo "<td>".$row['username']."</td><td>".$row['password']."</td>";
			echo "<tr><td>".$row['description']."</td>";
			echo "<td>".$row['start_date']."</td>";
			echo "<td>".$row['end_date']."</td>";
			echo "<td>".$row['code']."</td>";
			// echo "<td>".$row['comment']."</td>";
			echo "<td><a href='acs_menu.php?menu_id=".$row['id']."'>edit</a></td>";
			echo "<td><A href='acs_menu.php?delete_menu=".$row['id']."'>del</a></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}

function show_menu($menu_id)
{
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from MENUS");

	while ($row = mysql_fetch_array($result)) {
		
		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}
	
	$sql = "select * from MENUS where ID=".$menu_id;
	// echo $sql;
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);

	if ($result) {

		// echo "<form id='uploadMenuForm' method='POST' action='acs_menu.php'><table class='users'>";
		
		echo "<table><tr><td>OVERALL INFORMATION</td></tr>";
		echo "<tr><td>".$row['description']."</td>";
		echo "<td>".$row['code']."</td>";
		echo "<td>".$row['comment']."</td>";
		
		echo "<tr><td>DATE RANGE</td></tr>";
		echo "<tr><td>".date('F j Y',strtotime($row['start_date']));
		echo "<td>".date('F j Y',strtotime($row['end_date']));
		echo "</table>";
		/*
	//	while ($row = mysql_fetch_array($result) )
		{
			// echo "<tr><td>".$row['id']."</td>";
			foreach ($fieldnames as $i => $fieldname) {
				if ($fieldname == "id") {
					
				} 
				else if (substr($types[$fieldname],0,7) == "varchar" )
				{
					echo "<tr><td>".$fieldname."</td><td class='length_inputs'>";
					echo "<input name='".$fieldname."' value=\"".$row[$fieldname]."\"></td></tr>";
				}
				else {
					echo "<tr><td>".$fieldname."</td><td class='length_inputs'>";
					echo "<input name='".$fieldname."' value=\"".$row[$fieldname]."\"></td></tr>";
				}
			}
		}
		echo "</table>";
		echo "<input type='submit' name='update_menu' value='update_menu' class='draw_screen_btn users_submit'></form>";
		*/
		echo "<hr><h3>Menu items</h3>";
		echo "<form method='POST' action='acs_menu.php'><table width=100%>";
		echo "<tr><th>ITEM CODE</th><th>ITEM DESCRIPTION</th><th>PREP TYPE</th><th>SENSOR TYPE</th><th>PLATING TEAM</th><th></th></tr>";
		// can't use a join here because some menu_items don't have any components - they are a single item
		$sql = "select * from MENU_ITEMS where MENU_ID=".$menu_id;
		
		$menu_items = array();
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result) ) {
			
			$menu_items[] = $row;
		}
		$sql = "select * from PREP_TYPES order by ID";
		$result = mysql_query($sql);
		$prep_types = array();
		if ($result) {
			while($row = mysql_fetch_array($result)) {
				$prep_types[$row['id']] = $row['code'];
			}
		}
		foreach ($menu_items as $i => $row) {
		
			echo "<tr class='menu_item_row'><td>".$row['code']."</td>";
			echo "<td >".$row['dish_name']."</td><td></td><td></td><td>";
			select_plating_team($row['plating_team'],$row['id']);
			echo "</td></tr>";
		//	echo "<td><div class='acs_btn' onclick='add_menu_component(".$menu_id.",".$row['id'].",\"".$row['dish_name']."\");'><span>Add</span></div></tr>";
			$sql = "select * from MENU_ITEM_COMPONENTS where MENU_ITEM_ID=".$row['id'];
			$sql = "select * from MENU_ITEM_COMPONENTS, MENU_ITEM_LINK where MENU_ITEM_LINK.component_id = MENU_ITEM_COMPONENTS.id and MENU_ITEM_ID=".$row['id'];
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result) ) {
				echo "<tr><td></td><td>".$row['description']."</td>";
				$prep_type = $row['prep_type'];
				/* if (!empty($prep_type) && !empty($prep_types[$prep_type])) {
					$prep_type = $prep_types[$prep_type];
				}
				echo "<td>".$prep_type."</td>"; */
				echo "<td>";
				select_prep_type($prep_types,$row['prep_type'],$row['component_id']);
				echo "</td><td>";
				select_probe_type($row['probe_type'],$row['component_id']);
				
       			echo "</td>"; // plating team column
       			echo "<td><input type='checkbox' name='sub_components_".$row['component_id']."'></td>"; 
				//echo "<td><div class='acs_btn' onclick='del_menu_component(".$menu_id.",".$row['id'].",\"".$row['description']."\");'><span>Del</span></div></tr>";
			}
		}
		echo "<tr><td><input name='menu_item_code' size='10'></td>";
		echo "<td><input name='menu_item_dish_name' size='30'></td>";
		echo "</tr>";
		echo "</table>";
		echo "<input type=hidden name='menu_id' value='".$menu_id."'>";
		echo "<input type='submit' name='new_menu_item' value='New menu item' class='draw_screen_btn users_submit'></form>";
		//echo "<div id='add_menu_component_modal'><div class='modal_header'>Add menu item component</div>";
		//echo "<div id='menu_item_component_div'></div></div>";
		
	}
}
function select_plating_team($plating_team,$menu_item_id)
{
	echo "<select name='plating_team_".$menu_item_id."' onchange='update_plating_team(this,".$menu_item_id.");'>";
	echo "<option value='0'>-</option>";
	for ($i = 1; $i < 11; $i++) {
		echo "<option value='".$i."'";
		if ($i == $plating_team) { echo " selected"; }
		echo ">".$i."</option>";
	}
	echo "</select>";
}
function select_prep_type($preptypes,$prep_type_id,$comp_id)
{
	echo "<select name='pt_".$comp_id."' onchange='update_prep_type(this,".$comp_id.");'>";
	$idx = 1;
	foreach ($preptypes as $p) {
		echo "<option value='".$p."'";
		if ($idx++ == $prep_type_id) { echo " selected"; }
		echo ">".$p."</option>";
	}
	echo "</select>";
}

function select_probe_type($probe_type_id,$comp_id)
{
	echo "<select name='probe_".$comp_id."' onchange='update_probe_type(this,".$comp_id.");'>";
	$idx = 1;
	$probetypes = ['IR','Probe','N/A'];
	foreach ($probetypes as $p) {
		echo "<option value='".$p."'";
		if ($idx++ == $probe_type_id) { echo " selected"; }
		echo ">".$p."</option>";
	}
	echo "</select>";
}
function delete_menu()
{
	$del_id = $_POST['delete'];
	if ($del_id < 0) return;
	$sql = "delete from MENUS where ID=".$del_id;
	test_mysql_query($sql);
}
function update_menu()
{
	$menu_id = $_POST['menu_id'];
	if ($menu_id < 0) {
		return(new_user());
	}
	// $fieldnames = ["username","password","organisation","email","firstname","lastname"];
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from "."MENUS");
	while ($row = mysql_fetch_assoc($result)) {

		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}

	
	$sql = "update  MENUS set ";
	$n = 0;
	foreach ($fieldnames as $i => $fieldname) {
		if ($fieldname == "id") {
				
		}
		else if (substr($types[$fieldname],0,7) == "varchar" )
		{
			if ($n++ > 0) $sql .= ", ";
			$sql .= $fieldname."='".mysql_escape_string( $_POST[$fieldname])."'";
		}
	}

	$sql .= " where ID=".$menu_id;
	test_mysql_query($sql); 
}

function new_menu_item()
{
	$menu_id = $_POST['menu_id'];
	$sql = "insert into MENU_ITEMS ";
	$flds = "(id,menu_id,code,dish_name,plating_team)";
	$vals = " values (null,".$menu_id.",";
	$vals .= "'".mysql_escape_string( $_POST['menu_item_code'])."',";
	$vals .= "'".mysql_escape_string( $_POST['menu_item_dish_name'])."',";
	$vals .= "".mysql_escape_string( $_POST['menu_item_plating_team']).")";
	$sql .= $flds.$vals;
	// echo $sql;
	test_mysql_query($sql);
}

function add_menu_component()
{
	$menu_id = get_url_token('cc_menu_id');
	$menu_item_id = get_url_token('cc_menu_item_id');
	$prep_type = get_url_token('prep_type');
	$probe_type = get_url_token('probe_type');
	$sql = "insert into MENU_ITEM_COMPONENTS ";
	$flds = "(id,menu_item_id,description,prep_type,probe_type)";
	$vals = " values (null,".$menu_item_id.",";
	$vals .= "'".mysql_escape_string( get_url_token('menu_item_component_description'))."',".$prep_type.",".$probe_type.")";
	
	$sql .= $flds.$vals;
	// echo $sql;
	test_mysql_query($sql);
}

function del_menu_component()
{
	$menu_id = get_url_token('cc_menu_id');
	$mid = get_url_token('cc_menu_item_component_id');
	$prep_type = get_url_token('prep_type');
	$sql = "delete from MENU_ITEM_COMPONENTS where id=".$mid;
	
	// echo $sql;
	test_mysql_query($sql);
}

function new_menu()
{
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from "."MENUS");
	while ($row = mysql_fetch_assoc($result)) {

		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}


	$sql = "insert into MENUS ";
	$flds = "(id,";
	$vals = " values (null,";
	$n = 0;
	foreach ($fieldnames as $i => $fieldname) {
		if ($fieldname == "id") {
			// ignore - use autoincrement to assign an id
		}
		else if (substr($types[$fieldname],0,7) == "varchar" )
		{
		if ($n > 0) {
				$flds .= ",";
				$vals .= ",";
			}
			$flds .= $fieldname;
			
			$vals .= "\"".mysql_escape_string( $_POST[$fieldname])."\"";
		}
		$n++;
	}
	$flds .= ")";
	$vals .= ")";
	
	$sql .= $flds.$vals;
	test_mysql_query($sql);
}
?>
