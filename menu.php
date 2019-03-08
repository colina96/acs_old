<div id='menu_buttons1'>
	<div class="menu_buttons" >
	    <div class="menu_type" id="menu_status">
	        <button type='button' class='acs_menu_btn' href="#" id="active_menu"
	                onclick="show_active_menus()">ACTIVE</button>
	        <button type='button' class='acs_menu_btn' href="#" id="future_menu"
	                onclick="open_future_menus();">FUTURE</button>
	        <button type='button' class='acs_menu_btn' href="#" id="expired_menu"
	                onclick="openPage('future_menus', this, 'red','menu_details','acs_menu_btn')">EXPIRED</button>
	    </div>
	    
	   
	    <div class='acs_sidebar'> 
		
	        <button type='button' class='button_main' href="#" id="add_new_menu"
	                onclick="openPage('new_menu', this, 'red','menu_details','acs_menu_btn')">+ Add new menu</button>
	   </div>
	</div>
</div>
<div id='menu_buttons2'>
	<div class="menu_buttons">
		<div class="menu_type" onclick="show_active_menus();">
		<img onclick="show_active_menus();" class="return_icon" src="app/www/img/icon_arrow_black.png" <="" a="">
			<button type='button' class='acs_menu_btn' href="#" 
	                onclick="show_active_menus()">back</button>
		</div>
		<div class="acs_sidebar">
	    
		
	        
	    </div>
	</div>
</div>
<div class='acs_main'>

		<div class="acs_right_content">
			<!--  popup -->
			<div class='popup' id='del_item_popup'>
				<div class='h3'>Delete Item from Menu?</div>
				<table>
					<tr><td>Dish name</td><td><div id='del_item_dish_name'></div></td></tr>
					<tr><td>Code</td><td><div id='del_item_code'</div></td></tr>
				
				</table>
				<input type='hidden' id='del_item_id'>
				<input type='hidden' id='del_item_menu_id'>
				
				<div class='m-10'>
					<div class='btn' onclick='do_delete_item()'>Yes</div>
					<div class='btn' onclick="hide('del_item_popup');">No</div>
				</div>
			</div>
			<div class='popup' id='del_comp_popup'>
				<div class='h3'>Delete component?</div>
				<div id='del_comp_description'></div>
				<input type='hidden' id='del_comp_miid'>
				<input type='hidden' id='del_comp_menu_id'>
				<input type='hidden' id='del_comp_id'>
				<div class='m-10'>
					<div class='btn' onclick='do_delete_comp()'>Yes</div>
					<div class='btn' onclick='dont_delete_comp()'>No</div>
				</div>
			</div>
			<div class='popup' id='new_comp_popup'>
				<div class='h3'>New Component?</div>
				
				<input type='hidden' id='new_comp_miid'>
				<input type='hidden' id='new_comp_menu_id'>
				<input id='new_comp_description' style.width='400px'>
				<div class='m-10'>
					<div class='btn' onclick='do_new_comp()'>Add</div>
					<div class='btn' onclick='dont_new_comp()'>Cancel</div>
				</div>
			</div>
			<div class='popup' id='new_item_popup'>
				<div class='h3'>New Menu Item?</div>
				
				<input type='hidden' id='new_item_menu_id'>
				<table>
					<tr><td>Dish name</td><td><input id='new_item_dish_name'></td></tr>
					<tr><td>Code</td><td><input id='new_item_code'></td></tr>
				
				</table>
				
				<div class='m-10'>
					<div class='btn' onclick='do_new_item()'>Add</div>
					<div class='btn' onclick='dont_new_item()'>Cancel</div>
				</div>
			</div>
			
			<div id='new_menu' class='menu_details'>
				
				<h1>CREATE NEW MENU</h1>
				<form id='menuform' method="post" enctype="multipart/form-data">
                    <table>
                        <tr><td>OVERALL INFORMATION</td></tr>
                        <tr>
                            <td><input name='menu_name' type="text" class="menu_description" placeholder="Client"></td>
                            <td><input name='menu_description' type="text" class="menu_description" placeholder="Name"></td>
                            <td><input name='menu_comment' type="text" placeholder="Description"></td>
                        </tr>
                        <tr><td>DATE RANGE</td></tr>
                        <tr>
                            <td>
                                <input type="text"
                                       id="menu_start"
                                       name="menu_start"
                                       placeholder='Start date'
                                       class='datepicker'
                                       readonly="readonly">
                            </td>
                            <td>
                                <input type="text"
                                       id="menu_end"
                                       name="menu_end"
                                       placeholder='End date'
                                       class='datepicker'
                                       readonly="readonly">
                            </td>
                        </tr>
                        <tr>
                            <td colspan='3'>
                                <span id="select_file"> Select file to upload: </span>
                                <input type="file" name="fileToUpload" id="fileToUpload">
                                <!--   div class='drop-files-container' id='drop-files-container'></div -->
                                <input type='button' name='submit_menu'
                                       value='Submit menu' class='button_main' id="submit_menu" onclick='upload_menu();'>
                            </td>
                        </tr>
                    </table>
                </form>

		
			</div>
			<div id='active_menus' class='menu_details menu_details_active'></div>
			<div id='future_menus' class='menu_details'></div>
			<div id='expired_menus' class='menu_details'></div>
			
		
    </div>
</div>
<script>

    var menus = null;

function upload_menu()
{
	console.log('uploading menu');
	var form = document.getElementById('menuform');
	var formData = new FormData(form);
	$.ajax({
        url: "REST/upload_menu.php",
        type: 'POST',
        data: formData,
        success: function (data) {
            console.log(data)
        },
        cache: false,
        contentType: false,
        processData: false,
        success: function(result) { // need to get the id of the new component back to print labels
            console.log("upload_menu result ",result);
            show_active_menus();
        },
        fail: (function (result) {
            console.log("upload_menu fail ",result);
        })
    }); 
	return(false);
}

function select_plating_team(plating_team,menu_item_id)
{
	var ret =  "<select name='plating_team_" + menu_item_id + "' onchange='update_plating_team(this," + menu_item_id + ");'>";
	ret +=  "<option value='0'>-</option>";
	for (var i = 1; i < 11; i++) {
		ret += "<option value='" + i + "'";
		if (i == plating_team) { ret += " selected"; }
		ret += ">" + i + "</option>";
	}
	ret += "</select>";
	return(ret);
}
function menu_select_prep_type(preptypes,prep_type_id,comp_id,dock)
{
//	console.log('select_prep_type');
//	console.log(preptypes);
	
	var ret =  "<select name='pt_" + comp_id + "' onchange='menu_update_prep_type(this," + comp_id +");'>";
	var idx = 1;
	for (var i in preptypes) {
		// console.log(i);
		if ((!dock && preptypes[i].dock == 0) || (dock && preptypes[i].dock == 1)) { 
			ret += "<option value='" + i + "'";
			if (preptypes[i].id == prep_type_id) { ret += " selected"; }
			ret +=  ">"+ preptypes[i].code + "</option>";
		}
	}
	ret += "</select>";
	return (ret);
}

function select_probe_type(probe_type_id,comp_id)
{
	var ret =  "<select name='probe_" + comp_id + "' onchange='update_probe_type(this," + comp_id + ");'>";
	var idx = 1;
	var probetypes = ['IR','Probe','N/A'];
	for (var i in probetypes) {
		ret += "<option value='" + i + "'";
		if (idx++ == probe_type_id) { ret += " selected"; }
		ret += ">" + probetypes[i] + "</option>";
	}
	ret +=  "</select>";
	return (ret);
}

function format_date(date)
{
	return(date.substring(0,10));
}
function show_menu_details (div)
{
	// everything about this is ugly. rewrite TODO
	console.log(menu[active_menu_id]);
	
	var ret = "<table width=100%><tr><td class='user_subtitle' >OVERALL INFORMATION</td></tr>"
	ret += "<tr><td>Client</td>";
	ret += "<td>Name</td>";
	ret += "<td>Description</td></tr>";
	ret += "<tr><td>" + menu[active_menu_id]['description'] + "</td>";
	ret += "<td>" + menu[active_menu_id]['code'] + "</td>";
	ret += "<td>" + menu[active_menu_id]['comment'] + "</td>";
	
	ret += "<tr><td class='user_subtitle'>DATE RANGE</td></tr>";
	ret += "<tr><td>From: " + format_date(menu[active_menu_id]['start_date']);
	ret += "<td>To: " + format_date(menu[active_menu_id]['end_date']);
	ret += "<tr><td class='user_subtitle'>MENU ITEMS</td>";
	ret += "<td><button type='button' class='button_main' onclick='new_menu_item();'>+ Add new dish</button>";
	//search doesn't work here because the filter redraws everything - TODO
	ret += "<td><input type='text' id='menu_search' onkeyup='filter_menu(this)'  placeholder='Search'></td></tr>";
	ret += "</table>";
	return ret;
}

function filter_menu(search_fld)
{
	//console.log("menu search string ",search_fld.value.toUpperCase());
	// if (search_fld.value.length > 2) 
	show_menu(search_fld.value.toUpperCase());
}

function filter_menu_item(item,filter)
{
	if (!filter) return true;
	if (filter.length == 0) return true;
//	console.log('filter_menu_item ',filter.toUpperCase());
//	console.log(item);
	
	if (item['code'].toUpperCase().indexOf(filter) >= 0) return(true);
	if (item['dish_name'].toUpperCase().indexOf(filter) >= 0) return(true);
    if (item['components']) {
    	var components = item['components'];
        for (var c in components) {
           	var mid = components[c];
           	if (menu_item_components[mid].description.toUpperCase().indexOf(filter) >= 0) return(true);
        }
    }
	return (false);
}

function show_menu_and_header()
{
	show('menu_buttons2');
	hide('menu_buttons1');
	var div = document.getElementById('future_menus');
	div.innerHTML = show_menu_details(div);
	var items_div = document.createElement('div');
	items_div.id = 'menu_items_div';
	div.appendChild(items_div);
	show_menu();
}
function show_menu(filter)
{
	// show('search_menu_div');
	// openPage('menu_buttons2',this,'none','menu_buttons');
	//show('menu_buttons2');
	//hide('menu_buttons1');
	//clearChildren(anchor);
	var div = document.getElementById('menu_items_div');
	div.innerHTML = '';
	// clearChildren(div);
	// div.innerHTML = show_menu_details(div);
    var table = document.createElement('table');
    table.className = 'menu_table';
    table.width='100%';
    var tr = document.createElement('tr');
    var header = ['ITEM CODE','ITEM DESCRIPTION','PREP- LOCATION','STP','HR','PT','SENSOR TYPE','PLATING TEAM','SPLIT','',''];
    for (var i in  header) {
      //   console.log(i);
    	var th = document.createElement('th');
    	th.innerHTML = header[i];
    	tr.appendChild(th);
    } 
 /*   var th = document.createElement('th')
    th.innerHTML = "<div class='btn' id='add' onclick='new_menu_item();'>+</div>";
    tr.appendChild(th); */
    tr.appendChild(document.createElement('th'));
    table.appendChild(tr);
    
    var menu_fields = ['code','dish_name','','','','',''];
    for (var k in menu_items) {
    	var tr = document.createElement('tr');
    	tr.className = 'menu_item_row';
        var item = menu_items[k];
        if (filter_menu_item(item,filter)) {
	        for (var f in menu_fields) {
	        	var th = document.createElement('td');
	        	var fld = item[menu_fields[f]];
	        	if (!fld) fld = '';
	        	th.innerHTML = fld;
	        	tr.appendChild(th);
	        }
	        td = document.createElement('td');
	    	td.innerHTML = select_plating_team(item.plating_team,item.id);
	    	tr.appendChild(td);
	    	td = document.createElement('td');
	    	td.innerHTML = "<input type='number' maxlength='3' size='3' class='edit_location' name='split1_" + item.id + "'value='" +  item.split1 + "' onchange='set_db_field(this,\"MENU_ITEMS\",\"split1\"," + item.id + ");'>";
       		
	    	tr.appendChild(td);
	    	td = document.createElement('td');
	    	td.innerHTML = "<input type='number' maxlength='3' size='3' class='edit_location' name='split2_" + item.id + "'value='" +  item.split2 + "' onchange='set_db_field(this,\"MENU_ITEMS\",\"split2\"," + item.id + ");'>";
	    	tr.appendChild(td);
	    	td = document.createElement('td');
	    	td.innerHTML = "<input type='number' maxlength='3' size='3' class='edit_location' name='split3_" + item.id + "'value='" +  item.split3 + "' onchange='set_db_field(this,\"MENU_ITEMS\",\"split3\"," + item.id + ");'>";
	    	tr.appendChild(td);
	    	/*
	    	td = document.createElement('td');
	    	td.innerHTML = "<div class='btn' id='add' onclick='new_menu_item_component(" + item['id'] + ");'>+</div>";
	    	
	    	tr.appendChild(td);
			*/
	       	td = document.createElement('td');
	    	td.innerHTML += "<image class='icon' id='delete_dish' src='app/www/img/icon_delete_white.svg' onclick='del_menuitem("+ item['id'] + "," + k + ");'></image>";
	    	// td.colSpan = 2;
	    	tr.appendChild(td);
	        table.appendChild(tr);
	        
	        if (item['components']) {
	         //   console.log(item['components']);
	            var components = item['components'];
	            for (var c in components) {
	            	var tr = document.createElement('tr');
	            	var td = document.createElement('td');
	            	td.innerHTML = '';
	            	tr.appendChild(td);
	            	var td = document.createElement('td');
	            	var mid = components[c];
	            	if (!menu_item_components[mid])  {
	                	alert("cannot find component "  + mid + " " + item['dish_name']);
	            	}
	            	var innerHTML = "<div";
	            	if (menu_item_components[mid].high_risk == 1) {
	            	// 	innerHTML += " onclick='edit_high_risk_component(" + mid + ");'";
	            		
	            	}
	            	innerHTML += ">" + menu_item_components[mid].description + "</div>";
	            	td.innerHTML = innerHTML;
	            	td.width='50%';
	            	tr.appendChild(td);
	            	/* location */
	            	td = document.createElement('td');
	            	td.innerHTML = "<input type='text' maxlength='3' size='3' class='edit_location' name='location_" + mid + "'value='" +  menu_item_components[mid].location + "' onchange='set_location(this," + mid + ");'>";
	           		// td.innerHTML += 'location';
	            	tr.appendChild(td);
	            	// STP
	            	td = document.createElement('td');
	            	var innerHTML = "<input type='checkbox' value='1' name='label_at_dock_" + mid + "' onclick='set_db_field(this,\"MENU_ITEM_COMPONENTS\",\"label_at_dock\"," + menu_item_components[mid].id + ");'";
	            	if (menu_item_components[mid].label_at_dock == 1) {
	                	innerHTML += ' checked';
	            	}
	            	innerHTML += '>';
	            	td.innerHTML = innerHTML;
	            	tr.appendChild(td);
	            	// HR
	            	td = document.createElement('td');
	            	var innerHTML = "<input type='checkbox' name='high_risk_" + mid + "' onclick='set_high_risk(this," + mid + ");'";
	            	if (menu_item_components[mid].high_risk == 1) {
	                	innerHTML += ' checked';
	            	}
	            	innerHTML += '>';
	            	td.innerHTML = innerHTML;
	            	tr.appendChild(td);
	            	td = document.createElement('td');
	            	td.innerHTML = menu_select_prep_type(preptypes,menu_item_components[mid].prep_type,mid,false);
	            	tr.appendChild(td);
	            	td = document.createElement('td');
	            	td.innerHTML = select_probe_type(menu_item_components[mid].probe_type,mid);
	            	tr.appendChild(td);
	            	
	            	td = document.createElement('td');
	            	//td.innerHTML += "<div class='add_subcompdiv' onclick='add_subcomponent(" + mid + ");'>+ HR ingredient</div>";
	            	td.colSpan = 4;
	            	if (menu_item_components[mid].subcomponents) {
	                	// td.innerHTML += 'checked';
	            	}
	            	tr.appendChild(td);
	            	td = document.createElement('td');
	            	td.innerHTML += "<image class='icon' id='delete_component' src='app/www/img/icon_delete.svg' onclick='del_component(" + mid + ","+ item['id'] + ");'></image>";
	            	td.colSpan = 2;
	            	tr.appendChild(td);
	            	table.appendChild(tr);
	            	
	            	
	            }
	        }
	        else {
	            console.log('no components');
	        }
	        var tr = document.createElement('tr');
	    	// tr.className = 'menu_item_row';
	
		    td = document.createElement('td');
		    td.innerHTML = ' -- ';
		    
		    tr.appendChild(td);
		    td = document.createElement('td');
		    td.innerHTML = "<button type='button' class='button_main' onclick='new_menu_item_component(" + item['id'] + ");'>+ Add new component</button>";
		    	
		    tr.appendChild(td);
				
	         table.appendChild(tr);
        }
      //   console.log(k);
    }  
    var tr = document.createElement('tr');
	// tr.className = 'menu_item_row';

    td = document.createElement('td');
    td.innerHTML = ' -- ';
    
  
    div.appendChild(table);
}

var menu = null;
var menu_items = null;
var menu_item_components = null;
var preptypes = null;

function remove_subcomponent(menu_item_component_id,ingredient_id) 
{
	// console.log('remove_subcomponent ',menu_item_component_id,ingredient_id) ;
	if (!menu_item_components[menu_item_component_id].subcomponents) {
 		console.log('ERROR - no subcomponents');
 		return;
 	}

 	for ( var i = 0; i < menu_item_components[menu_item_component_id].subcomponents.length; i++) {
 		if ( menu_item_components[menu_item_component_id].subcomponents[i] == ingredient_id) {
 			menu_item_components[menu_item_component_id].subcomponents.splice(i,1);
 			show_menu();
 			remove_ingredient(active_menu_id,menu_item_component_id,ingredient_id)
 			return;
 		}	
 	}
 	console.log('ERROR - subcomponent not found',ingredient_id);
	
}

function set_split(input,split,id)
{
	console.log('set_split',input.value,split,id);
	var d = new Object();
	var data = Object();
	data['id'] = id;
	data['split' + split ] = input.value;
	d.data = data;
	d.TABLENAME = 'MENU_ITEMS';
	d['action'] = 'UPDATE';
	var data =  {data: JSON.stringify(d)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "replace.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("set_split result ",result);
            if (result.indexOf('error') >= 0)
            {
                let div = document.createElement('div');
                div.innerHTML = result;
                document.body.appendChild(div);
            }
 
        },
        fail: (function (result) {
            console.log("set_location fail ",result);
        })
    });
}

function reload_menu()
{
	load_menu(active_menu_id);
}

function set_db_field(input,tablename,field,id,callback)
{
	console.log('set_db_field',input,tablename,field,id,input.type);
	var d = new Object();
	var data = Object();
	data['id'] = id;
	if (input.type == 'checkbox') 
		data[field] = input.checked?1:'false';
	else
		data[field] = input.value;
	d.data = data;
	d.TABLENAME = tablename;
	d['action'] = 'UPDATE';
	var data =  {data: JSON.stringify(d)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "replace.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("set_db_field result ",result);
            
            if (result.indexOf('error') >= 0)
            {
                let div = document.createElement('div');
                div.innerHTML = result;
                document.body.appendChild(div);
            }
            else {
                if (callback) callback();
            	// load_menu(active_menu_id);
            }
        },
        fail: (function (result) {
            console.log("set_location fail ",result);
        })
    });
}
function set_location(input,id)
{
	console.log('set_location',input.value,id);
	var d = new Object();
	d['id'] = id;
	d['location' ] = input.value;
	var data =  {data: JSON.stringify(d)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "set_component_location.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("iset_location result ",result);
 
        },
        fail: (function (result) {
            console.log("set_location fail ",result);
        })
    });
}

function insert_ingredient(menu_id,component_id,subcomponent_id)
{
	
	var component = new Object();
	component['menu_id'] = menu_id;
	component['component_id'] = component_id;
	component['subcomponent_id'] = subcomponent_id;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "link_ingredient.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("insert_ingredient result ",result);
 
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
}

function do_delete_item()
{
	hide('del_item_popup');
	var component = new Object();
	component['menu_id'] = active_menu_id;
	component['id'] = document.getElementById('del_item_id').value;
	
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "delete_menu_item.php",
        type: "POST",
        data: data,

        success: function(result) { 
            console.log("insert_ingredient result ",result);
            load_menu(active_menu_id);
 
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
	
}
function del_menuitem(item_id,idx)
{
	console.log('del_menuitem ',item_id);
	console.log(menu_items[item_id]);
	document.getElementById('del_item_id').value = item_id;
	document.getElementById('del_item_dish_name').innerHTML = menu_items[item_id].dish_name;
	document.getElementById('del_item_code').innerHTML = menu_items[item_id].code;
	show('del_item_popup');
}
function remove_ingredient(menu_id,component_id,subcomponent_id)
{
	
	var component = new Object();
	component['menu_id'] = menu_id;
	component['component_id'] = component_id;
	component['subcomponent_id'] = subcomponent_id;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "unlink_ingredient.php",
        type: "POST",
        data: data,

        success: function(result) { 
            console.log("insert_ingredient result ",result);
            load_menu(active_menu_id);
 
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
}

function do_delete_comp()
{
	hide('del_comp_popup');
	var component = new Object();
	component['menu_item_id'] = document.getElementById('del_comp_miid').value;
	component['menu_id'] = document.getElementById('del_comp_menu_id').value;
	component['component_id'] = document.getElementById('del_comp_id').value;
	
	var data =  {data: JSON.stringify(component)};
    console.log("do_delete_comp Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "unlink_comp.php",
        type: "POST",
        data: data,

        success: function(result) { 
            console.log("insert_ingredient result ",result);
            load_menu(active_menu_id);
 
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
}

function dont_delete_comp()
{
	hide('del_comp_popup');
}
function del_component(comp_id,menu_item_id)
{
	console.log('delete component',comp_id,menu_item_id);
	var comp = menu_item_components[comp_id];
	console.log(comp);
	document.getElementById('del_comp_description').innerHTML = comp.description;
	document.getElementById('del_comp_miid').value = menu_item_id;
	document.getElementById('del_comp_menu_id').value = comp.menu_id;
	document.getElementById('del_comp_id').value = comp_id;
	show('del_comp_popup');
}

function do_new_comp()
{
	hide('new_comp_popup');
	var component = new Object();
	component['menu_item_id'] = document.getElementById('new_comp_miid').value;
	component['menu_id'] = document.getElementById('new_comp_menu_id').value;
	component['description'] = document.getElementById('new_comp_description').value;

	var data =  {data: JSON.stringify(component)};
    console.log("do_new_comp Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "new_menu_item_comp.php",
        type: "POST",
        data: data,

        success: function(result) { 
            console.log("do_new_comp result ",result);
            load_menu(active_menu_id);
        },
        fail: (function (result) {
            console.log("do_new_comp fail ",result);
        })
    });
}

function dont_new_comp()
{
	hide('new_comp_popup');
}

function do_new_item()
{
	
	var component = new Object();
	component['menu_id'] = active_menu_id;
	component['dish_name'] = document.getElementById('new_item_dish_name').value;
	component['code'] = document.getElementById('new_item_code').value;
	if (component['dish_name'] && component['dish_name'].length > 2 && component['code'] && component['code'].length > 2) {
		hide('new_item_popup');
	}
	else {
		// error or warning message????? TODO
		console.log('invalid values for new menu item');
		return;
	}
	var data =  {data: JSON.stringify(component)};
    console.log("do_new_comp Sent Off: %j", data);
 
    $.ajax({
        url: RESTHOME + "new_menu_item.php",
        type: "POST",
        data: data,

        success: function(result) { 
            console.log("do_new_item result ",result);
            load_menu(active_menu_id);
        },
        fail: (function (result) {
            console.log("do_new_item fail ",result);
        })
    });
}

function dont_new_item()
{
	hide('new_item_popup');
}
function new_menu_item_component(menu_item_id)
{
	console.log('new component',menu_item_id,active_menu_id);
	
	document.getElementById('new_comp_description').value = '';
	document.getElementById('new_comp_miid').value = menu_item_id;
	document.getElementById('new_comp_menu_id').value = active_menu_id;
	
	show('new_comp_popup');
	
	$('#new_comp_description').val('');
	var data = Array();
	for (var c in menu_item_components) {
		var d = Array();
		d.label = menu_item_components[c].description;
		d.value = menu_item_components[c].id;
		data.push(d);
	}
	$('#new_comp_description').autocomplete({
        // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
        minLength: 2,
		source: data,
		// Once a value in the drop down list is selected, do the following:
		response: function( event, ui ) { 
        			console.log("search response found " + ui.content.length); console.log(ui);
        			if (ui.content.length == 0) {
        				// document.getElementById('new_comp_btns').style.display = 'block';
        			}
        			else {
        				// document.getElementById('new_comp_btns').style.display = 'none';
        			}
        		},
        select: function(event, ui) {
        	
            // place the person.given_name value into the textfield called 'select_origin'...
            $('#new_comp_description').val(ui.item.label);
            // and place the person.id into the hidden textfield called 'link_origin_id'. 
         	console.log('selected ',ui.item.value);
         	
           //  hide('add_sub_popup');
         	
         // 	show_menu_item_components(ui.item.value);
            return false;
        }
	
    })
}

function new_menu_item()
{
	console.log('new menu item',active_menu_id);
	
	document.getElementById('new_item_dish_name').value = '';
	document.getElementById('new_item_code').value = '';
	document.getElementById('new_item_menu_id').value = active_menu_id;
	
	show('new_item_popup');
}
function set_high_risk(checkbox,menu_item_component_id) 
{
	var component = new Object();
	component.menu_id = active_menu_id;
	component.menu_item_component_id = menu_item_component_id;
	component.high_risk = checkbox.checked?1:0;
	
	// component.prep_type = new_comp['prep_type'];
	
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    $.ajax({
        url: RESTHOME + "set_high_risk.php",
        type: "POST",
        data: data,

        success: function(result) { 
        	console.log(result);
        	load_menu(active_menu_id)
        	// component_selected();
        },
        fail: (function (result) {
            console.log("new _component fail ",result);
        })
    });
}

function inval(input_name)
{
	try {
		var ele = document.getElementsByName(input_name)[0];
		console.log(ele.type);
		if (ele.type == 'checkbox') {
			return(ele.checked?1:0);
		}
		return(document.getElementsByName(input_name)[0].value);
	}
	catch (e) {
		console.log(e);
		console.log('cannot read ' + input_name);
	}
}
function new_subcomponent() {
	hide('add_sub_popup');
	console.log('adding ' + $('#comp_description').val());
	var component = new Object();
	component.description = $('#comp_description').val();
	component.menu_id = active_menu_id;
	component.menu_item_component_id = active_menu_item_component_id; 
	component.id = inval('comp_id');
	component.location = inval('location');
	component.high_risk = inval('comp_high_risk');
	component.supplier = inval('comp_supplier');
	component.product = inval('comp_product');
	component.spec = inval('comp_spec');
	component.PT_id = inval('comp_PT_id');
	component.prep_type = inval('comp_prep_type');
	component.product = inval('comp_product');
	component.shelf_life_days = inval('comp_shelf_life_days');

	console.log(component);
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    $.ajax({
        url: RESTHOME + "new_subcomponent.php",
        type: "POST",
        data: data,

        success: function(result) { 
        	console.log(result);
        	load_menu(active_menu_id)
        	// component_selected();
        },
        fail: (function (result) {
            console.log("new _component fail ",result);
        })
    });
}

function edit_high_risk_component(menu_item_component_id)
{
	// show('edit_high_risk_popup');
	console.log(menu_item_components[menu_item_component_id]);
	document.getElementById('component_title').innerHTML = 'Edit component';
	var comp = menu_item_components[menu_item_component_id];
	for (var c in comp) {
		console.log(c + " => " + comp[c]);
		if (!comp[c] || comp[c] == null || comp[c] == 'null') {
			comp[c] = '';
		}
		if (document.getElementById("comp_" + c)) {
			var ele = document.getElementById("comp_" + c);
			console.log('element ' + ele.id + " type:" + ele.type);
			document.getElementById("comp_" + c).value = comp[c];
		}
		else if (document.getElementsByName("comp_" + c) && document.getElementsByName("comp_" + c)[0]) {
			var ele = document.getElementsByName("comp_" + c)[0];
			console.log('element ' + ele.name + " type:" + ele.type);
			if (ele.type == 'checkbox') {
				ele.checked = comp[c] == 1?true:false;
			}
			else {
				document.getElementsByName("comp_" + c)[0].value = comp[c];
			}
		} else 
		{
			console.log('error ' + "comp_" + c);
		}
	}	
	show('add_sub_popup');
}

var active_menu_item_component_id = null;
function clear_flds(flds)
{
	for (var i = 0; i < flds.length; i++ ) {
		if (document.getElementsByName(flds[i])) {
			document.getElementsByName(flds[i])[0].value = '';
		}
	}
}
function add_subcomponent(menu_item_component_id)
{
	console.log('add_subcomponent');
	show('add_sub_popup');
	document.getElementById('component_title').innerHTML = 'Add high risk ingredient';
	active_menu_item_component_id = menu_item_component_id;
	console.log('add_subcomponent ' + menu_item_component_id);
	clear_flds(['comp_id','comp_supplier','comp_product','comp_spec']);
	$('#comp_description').val('');
	var data = Array();
	for (var c in menu_item_components) {
		var d = Array();
		d.label = menu_item_components[c].description;
		d.value = menu_item_components[c].id;
		data.push(d);
	}
	$('#comp_description').autocomplete({
        // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
        minLength: 2,
		source: data,
		// Once a value in the drop down list is selected, do the following:
		response: function( event, ui ) { 
        			console.log("search response found " + ui.content.length); console.log(ui);
        			if (ui.content.length == 0) {
        				// document.getElementById('new_comp_btns').style.display = 'block';
        			}
        			else {
        				// document.getElementById('new_comp_btns').style.display = 'none';
        			}
        		},
        select: function(event, ui) {
        	
            // place the person.given_name value into the textfield called 'select_origin'...
            $('#comp_description').val(ui.item.label);
            // and place the person.id into the hidden textfield called 'link_origin_id'. 
         	console.log('selected ',ui.item.value);
         	if (!menu_item_components[menu_item_component_id].subcomponents) {
         		menu_item_components[menu_item_component_id].subcomponents = Array();
         	}
            console.log('adding to components');
            menu_item_components[menu_item_component_id]['subcomponents'].push(ui.item.value);
            insert_ingredient(active_menu_id,menu_item_component_id,ui.item.value);
            show_menu();
            hide('add_sub_popup');
         	
         // 	show_menu_item_components(ui.item.value);
            return false;
        }
	
    })
}

function done_add_sub()
{
	hide('add_sub_popup');
}

function cancel_add_sub()
{
	hide('add_sub_popup');
}
var menu = null;
var menu_items = null;
var menu_item_components = null;
var preptypes = null;
function open_future_menus() // dummy code for now
{
	var div = openPage('future_menus', this, 'red','menu_details','acs_menu_btn');
	div.innerHTML = null;
}
var active_menu_id = null;

function delete_menu(menu_id)
{
	console.log("deleting menu " + RESTHOME + "delete_menu.php?menu_id=" + menu_id);
	$.ajax({
	   url: RESTHOME + "delete_menu.php?menu_id=" + menu_id,
   		type: "POST",
   		// dataType: 'json',
   		success: function(result) {     
       console.log("delete menu success got " + result + " ");
   			show_active_menus();
   		},
	   fail: (function (result) {
	       console.log("fail delete_menu",result);
	   })
	});
}


function load_menu(menu_id)
{
	active_menu_id = menu_id;
	openPage('future_menus', this, 'red','menu_details','acs_menu_btn');
	console.log("loading menu " + RESTHOME + "get_menu.php?menu_id=" + menu_id);
	 $.ajax({
    url: RESTHOME + "get_menu.php?menu_id=" + menu_id,
    type: "POST",
   // data: data,
   //  data: {points: JSON.stringify(points)},
    dataType: 'json',
    // contentType: "application/json",
    success: function(result) {     
     //   console.log("success got " + result + " ");
        menu = result['menu'];
        menu_items = result.menu_items;
        menu_item_components = result.menu_item_components;
        preptypes = result.preptypes;
        show_menu_and_header();
 //       console.log(menu);
 //       console.log(preptypes);
 
      //  console.log(result);
        
    },
    done: function(result) {
        console.log("done preptypes ");
    },
    fail: (function (result) {
        console.log("fail preptypes",result);
    })
});
}
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


function menu_update_prep_type(s,comp_id)
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
	hide('menu_buttons2');
	show('menu_buttons1');
	// document.getElementById('menu_search').value = '';
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
		div.innerHTML = "<h3>No Active Menus</h3>";
		return;
	}
	div.innerHTML = "";
	var tab = document.createElement('table');
	tab.className = 'menu_table';
	tab.width = '100%';
	tab.border = '0';
	var tr = document.createElement('tr');
	tr.appendChild(new_td('Client','comp'));
	tr.appendChild(new_td('Start','comp'));
    tr.appendChild(new_td('End','comp'));
    tr.appendChild(new_td('Name','comp'));
   // tr.appendChild(new_td('Edit','comp'));   
    tr.appendChild(new_td('Delete','comp'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		tr.setAttribute(
				"onclick",
				"load_menu(" + data[i]['id'] + ");"
			);
   		tr.appendChild(new_td(data[i]['description'],'comp'));
   		var start_date = new Date(data[i]['start_date']);
   		tr.appendChild(new_td(show_date(start_date),'comp'));
   		var end_date = new Date(data[i]['end_date']);
   		tr.appendChild(new_td(show_date(end_date),'comp'));
   		tr.appendChild(new_td(data[i]['code'],'comp'));
   		// tr.appendChild(new_td("<a href='acs_menu.php?menu_id=" + data[i]['id'] + "'>edit</a>",'comp'));
   		//var btn = "<div class='btn' onclick='load_menu(" + data[i]['id'] + ");'>Edit</div>";
   		//tr.appendChild(new_td(btn,'comp'));
   		var btn = "<image class='icon' id='delete_menu' src='app/www/img/icon_delete.svg' onclick='delete_menu(" + data[i]['id'] + ");'></image>";
   		tr.appendChild(new_td(btn,'comp'));
   		//var del = "<a href=acs_menu?delete_menu=" + data[i]['id'] + ">&#x274c</a>";
   		//tr.appendChild(new_td(del,'comp'));
   		// tr.appendChild(new_td("<button type='button' class='acs_comp_btn' onclick='act_component(" + i + ");'>Action</button>",'comp'));
   		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}

</script>


<script src="navigation_select.js"></script>

