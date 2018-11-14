var default_tab='m_current_tracking_tab';
var comps = null;
var plating_comps = null; // components in cool room
var preptypes = null;

var plating_teams = null;
var plating_item = null;
var active_plating_team = 0;
var active_comp = null; // the component currently being worked on
var active_menu_item_id = null;
var new_comp = null; // start a new component - M1

var RESTHOME = "http://10.0.0.32/acs/REST/";

var barcode_mode = null;
var mode = null; // kitchen or plating
var plating_prep_type = 5; // AHR

function copy_object(o)
{
	return(JSON.parse(JSON.stringify(o)));
}


function set_barcode_mode(mode)
{
	console.log('set_barcode_mode',mode);
	barcode_mode = mode;
	keyboard_str = '';
	document.getElementsByName('kitchen_manual_barcode')[0].value = '';
	
}

function process_barcode(s)
{ 
	console.log("process_barcode " + s + " mode " + barcode_mode);
	if (s == 'setup1') {
		console.log('setup');
	}
	if (barcode_mode == null) {
		return;
	}
	if ((s.indexOf('u') >= 0) || (s.indexOf('U') >= 0)) { // user barcode scanned
		var uid = parseInt(s.substring(4));
		if (barcode_mode == 'login') {
			login(uid);
		}
		if (barcode_mode == 'dock_QA') {
			dock_QA_scan(uid);
		}
		if (barcode_mode == 'M1') {
			set_user('m1_chef_id','m_temp_modal4',uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'M1_LR') {
			set_user('m1_chef_id_LR','m_temp_modal4',uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'PT') { // plating team member
			add_team_member(uid);
		}
	}
	if ((s.indexOf('c') >= 0) || (s.indexOf('C') >= 0)) { // component barcode scanned
		var cid = parseInt(s.substring(3));
		if (barcode_mode == 'PT_comp') {
			plating_comp_barcode_scanned(cid);
		}
		if (barcode_mode == 'active_comp') {
			console.log('loogin for ' + cid);
			for (var i = 0; i < active_comps.length; i++) {
				if (active_comps[i].id == cid) {
					active_comp_selected(i);
					barcode_mode = null;
				}
			}
		}
		if (barcode_mode == 'kitchen_reprint') {
			for (var i = 0; i < active_comps.length; i++) {
				if (active_comps[i].id == cid) {
					reprint_active_comp_labels(i);
					barcode_mode = null;
				}
			}
		}
		if (barcode_mode == 'dock_reprint') {
			reprint_dock_labels(cid);
			barcode_mode = null;
		}
		if (barcode_mode == 'scan_ingredients') {
			console.log('read ingredient ' + cid);
			check_ingredient(cid);
		}
	}
}

function set_ingredient_temp(s)
{
	var i = new_comp['read_temp'];
	new_comp['selected_ingredients'][i]['temp'] = s;
	if (draw_ingredients()) {
		// save ingredient - new_comp.php
		start_component(false,true);
	}
}

function check_ingredient(cid)
{
	
	console.log('check ingredient'  + cid);
	document.getElementById('m1_temp_div_1_error').innerHTML = '';
	$.ajax({
        url: RESTHOME + "get_active_comps.php?cid=" + cid,
        type: "POST",

        success: function(result) {
        	console.log(result);
 
            var scanned_ingredient = JSON.parse(result);
            console.log("got component " + scanned_ingredient[0].description);
            var valid_ingredient = false;
            for (var i = 0; i < new_comp['selected_ingredients'].length; i++) {
    			var sub = get_component_by_id(new_comp['selected_ingredients'][i]['id']);
    			if (sub['description'] == scanned_ingredient[0].description) {
    				console.log("found ingredient");
    				valid_ingredient = true;
    				new_comp['selected_ingredients'][i]['cid'] = scanned_ingredient[0].id;
    				// attach to new_comp and record temperature
    				draw_ingredients();
    				new_comp['read_temp'] = i;
    				read_temp('M0');
    				
    			}
    			
    		}
            if (!valid_ingredient) {
				document.getElementById('m1_temp_div_1_error').innerHTML = 'invalid component';
			}
            
        },
        fail: (function (result) {
            console.log("fail check_ingredient ",result);
        })
    });
}

function load_preptypes()
{
console.log("loading prep types");
    $.ajax({
        url: RESTHOME + "get_preptypes.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            preptypes = result;
            
            console.log("got " + result.length + " preptypes");
            
        },
        done: function(result) {
            console.log("done preptypes ");
        },
        fail: (function (result) {
            console.log("fail preptypes",result);
        })
    });
}

function goto_home()
{
	openPage('mm1', this, 'red','mobile_main','tabclass');
}

function goto_plating_teams()
{
	
	load_menu_items();
	load_plating_items();
	mode = 'plating';
	openPage('plating_div', this, 'red','mobile_main','tabclass');
	openPage('m_sel_team', this, 'red','m_modal','tabclass');
	document.getElementById('plating_comment_div').innerHTML = '';
}

function save_plating_team()
{
	var data =  {data: JSON.stringify(plating_teams)};
	console.log("Sent Off: %j", data);
	set_barcode_mode (null);
	goto_plating();
    $.ajax({
        url: RESTHOME + "save_plating_teams.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log('save_plating_team',result);
           //  goto_m_main();
        },
        fail: (function (result) {
            console.log('save_plating_team',result);
        })
    });
    
}

function show_plating_items(team_id,tab)
{
	var item_count = 0;
	console.log('show_plating_items');
	for (var i = 0; i < menu_items.length; i++) {
		if (menu_items[i]['plating_team'] == team_id) {
			if (item_count == 0 && team_id != active_plating_team) {
				var tr = document.createElement('tr');
				tr.className = 'plating_tab';
				var td = document.createElement('th');
				td.innerHTML = margin('TEAM');
				tr.appendChild(td);
				td = document.createElement('th');
				td.innerHTML = team_id;
				tr.appendChild(td);
				tab.appendChild(tr);
			}
			item_count ++;
			
			var plating_item = get_plating_item_by_menu_item_id(menu_items[i]['id']);
			console.log(plating_item);
			tr = document.createElement('tr');
			var td = document.createElement('td');
			td.innerHTML = margin(menu_items[i]['code']);
			tr.appendChild(td);
			td = document.createElement('td');
			var div = "<div onclick='show_menu_item_components(" + menu_items[i]['id'] + ");'>" + menu_items[i]['dish_name']; + "</div>"
			if (plating_item && plating_item.time_started) {  // check plating_item.checked
				div = "<div class='orange' onclick='show_plating_options(" + plating_item.id + ");'>" + menu_items[i]['dish_name']; + "</div>";
				if (plating_item.time_completed) {
					div = "<div class='red'>" + menu_items[i]['dish_name']; + "</div>";
				}
			}

			td.innerHTML = margin(div);
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = margin(menu_items[i]['s1']);
			tr.appendChild(td);
			tab.appendChild(tr);
		}
	}
}
function goto_plating()
{
	
	// ???? load_menu_items();
	show('plating_return');
	hide('plating_print_labels');
	if (active_plating_team == null) {
		goto_plating_teams();
	}
	openPage('m_plating', this, 'red','mobile_main','tabclass');
	openPage('m_plating_sched', document.getElementById('m_plating_team_tab'), 'red','m_modal','tabclass');
	var hd = document.getElementById('active_plating_team_head');
	hd.innerHTML = "Plating Team " + active_plating_team;
	var t = document.getElementById('plating_sched_list');
	t.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'plating_tab';
	var tr = document.createElement('tr');
	tr.className = 'plating_tab';
	var th = document.createElement('th');
	th.innerHTML = margin('CODE');
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML = 'PRODUCT NAME';
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML = 'QTY';
	tr.appendChild(th);
	tab.appendChild(tr);
	show_plating_items(active_plating_team,tab);
	
	// now list all items for other plating teams
	for (var pti = 1; pti < plating_teams.length; pti++ ) {
		if (pti != active_plating_team)
			show_plating_items(pti,tab);
		
	}
	t.appendChild(tab);
}

function show_plating_options(id)
{
	plating_item = get_plating_item_by_id(id);
	console.log("show_plating_options " + id);
	console.log(plating_item);
	document.getElementById('plating_options_item_div').innerHTML = plating_item.dish_name;
	openPage('m_plating_options', this, 'red','m_modal','tabclass');
}

function calc_time_remaining(item)
{
	
	var event_time = new Date(item['time_started']);
	console.log("calc_time_remaining -" + event_time + " - " + item['time_started']);
	console.log(item);
	var remaining = 0;
	var now = new Date();
	var now_ms = now.getTime();
	var event_ms = event_time.getTime(); // time in millisecs
	
	var due_min = get_preptype_val(plating_prep_type,'M2_time_minutes');
	var due_ms = event_ms + due_min * 60 * 1000;  			
	remaining = (due_ms - now_ms) / (60 * 1000);
	console.log("M2_due_min M1_ms",due_min,event_ms,due_ms,format_minutes(remaining));
	return(format_minutes(remaining));
}

function finish_plating()
{ // switch to modal to record M2 temperature
	
	// calculate time remaining
	document.getElementById('ms_2').innerHTML = 'M2';
	document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
	var sign = ' < ';
	document.getElementById('ms_2_target').innerHTML = sign + get_preptype_val(plating_prep_type,'M2_temp') + "&#176";
	document.getElementById('chk_temp_item_div').innerHTML = '' ; // new_comp['description'];

	// openPage('m_temp_modal', this, 'red','m_modal2','tabclass');
	// document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];
	document.getElementById('ms_1').innerHTML = 'M2';
	document.getElementById('ms_1_text').innerHTML = calc_time_remaining(plating_item);
	

	document.getElementById('chk_temp_pt_div').innerHTML = plating_item.code;
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	openPage('m2_temp_plating', this, 'red','m_modal2','tabclass');
	
}
function record_finish_plating()
{
	console.log("finish_plating");
	console.log(plating_item);
	var data =  {data: JSON.stringify(plating_item)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: RESTHOME + "finish_plating.php",
        type: "POST",
        data: data,

        success: function(result) {
        	plating_item.time_completed = result;
            console.log("print_plating_labels result ",result);
            goto_plating();
        },
        
        fail: (function (result) {
            console.log("print_plating_labels fail ",result);
        })
    });
}



function plating_comp_selected(i)
{
	var menu_item = plating_item; // get_menu_item_by_id(active_menu_item_id);
	plating_item.active_item = i;
	var items = menu_item.items;
	if (i >= 0 && i < items.length) {
		console.log("selected " + items[i].description);
	}
	openPage('m_plating_temp', this, 'red','m_modal','tabclass');
	document.getElementById('chk_plating_item_temp_div').innerHTML = items[i].description;
}

function plating_comp_barcode_scanned(barcode_id) {
	console.log('plating_comp_barcode_scanned '  + barcode_id);
	// find item in active components
	var description = null;
	for (var i = 0; i < plating_comps.length; i++) {
		if (plating_comps[i].id == barcode_id) {
			console.log("plating_comp_barcode_scanned found " + plating_comps[i].description + ' ' + plating_comps[i].expired);
			if (plating_comps[i].expired == 1) {
				console.log('item expired');
				document.getElementById('m2_pt_sl_div2').innerHTML = 'expired ' + plating_comps[i].expiry_date;
				openPage('m_temp', this, 'red','mobile_main','tabclass');
				openPage('m2_sl_plating', this, 'red','m_modal2','tabclass');
				
				return; // jump to expired page
			}
			description = plating_comps[i].description;
		}
	}
	var items = plating_item.items;
	console.log("now checking plating item " + items.length);
	for (var i = 0; i < items.length; i++) {
		if (items[i].description == description && !items[i].M1_temp) {
			items[i].checked = true;
			items[i].component_id = barcode_id;
			console.log("found item");
			plating_comp_selected(i);
		}		
	}
}
function goto_active_plating()
{
	show_menu_item_components(active_menu_item_id)
}


function set_plating_M1_temp(temperature) 
{
	console.log("set_plating_M1_temp " + plating_item.description + " -> " + 
			plating_item.items[plating_item.active_item].description);
	// check temp is below M1_temp
	
	var temp_target = get_preptype_val(plating_prep_type,'M1_temp');
	if (temperature < temp_target) {
		plating_item.items[plating_item.active_item].M1_temp = temperature;
		goto_active_plating();
	}
	
}

function set_plating_M2_temp(temperature) 
{
	console.log("set_plating_M2_temp " );
	console.log(plating_item);
	plating_item.M2_temp = temperature;
	var temp_target = get_preptype_val(plating_prep_type,'M2_temp');
	if (parseInt(temperature) <= temp_target) {
		console.log('record_finish_plating()');
		record_finish_plating();
	}
	else {
		console.log("plating m2 too high");
	}
}

function show_menu_item_components(menu_item_id) {
	get_comps_for_plating(menu_item_id);
}

function show_plating_comps(description)
{
	console.log("show_plating_comps " + description);
	if (plating_comps == null || plating_comps.length == 0) {
		return ("ERROR none available");
	}
	var ret = '';
	for (var i = 0; i < plating_comps.length; i++) {
		if (plating_comps[i].description === description) {
			ret += "<br>" + plating_comps[i].expiry_date + ' (' + plating_comps[i].id + ')';
		}
	}
	if (ret == '') return ("none available");
	return (ret);
}


function find_plating_item(menu_item_id)
{
	console.log('find_plating_item ' + menu_item_id);
	if (plating_items == null) plating_items = Array();
	for (var i = 0; i < plating_items.length; i++) {
		
		if (plating_items[i].menu_item_id == menu_item_id) {
			console.log('found plating_item ' + menu_item_id);
			return plating_items[i];
		}
	}
	var mi = get_menu_item_by_id(menu_item_id);
	console.log(mi);
	plating_item = copy_object(mi); 
	plating_item.id = null; // get this when inserted into db
	plating_item.menu_item_id = menu_item_id;
	plating_items.push(plating_item);
	console.log(plating_item);
	return(plating_item);
}

function print_plating_labels()
{
	
	console.log(" print_plating_labels ");

//	var p = Object.assign({}, active_comp);
//	comp.copies = qty;
	plating_item.trolley_labels = parseInt(document.getElementById('trolley_labels').innerHTML);
	plating_item.description_labels = parseInt(document.getElementById('pt_description_labels').innerHTML);
	var data =  {data: JSON.stringify(plating_item)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: RESTHOME + "plating_labels.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log("print_plating_labels result ",result);
            
        },
        
        fail: (function (result) {
            console.log("print_plating_labels fail ",result);
        })
    });
	goto_plating_teams();
	
}

function start_plating_item()
{
	
	console.log('start_plating_item',plating_item['M1_time'],plating_item['id']);
	
	if (plating_item['id']) {
		console.log('already started');
		print_plating_labels();
		// reprint labels
		return;
	}

//	var p = Object.assign({}, active_comp);
//	comp.copies = qty;
	plating_item.trolley_labels = parseInt(document.getElementById('trolley_labels').innerHTML);
	plating_item.description_labels = parseInt(document.getElementById('pt_description_labels').innerHTML);
	var data =  {data: JSON.stringify(plating_item)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: RESTHOME + "new_plating_item.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log("new_plating_item result ",result);
            var p = JSON.parse(result);
            plating_item['plating_item_id'] = p['id'];
            plating_item['id'] = p['id'];
            plating_item['M1_time'] = p['M1_time'];
            
            
            // prob should store plating_item_component ids but not needed at this point
            // print labels
            print_plating_labels();
        },
        
        fail: (function (result) {
            console.log("new_plating_item fail ",result);
        })
    });
	// goto_plating_teams();
	
}

function reprint_plating_labels()
{
	var div = document.getElementById('plating_sched_list');
	div.innerHTML = '<div class="modal_head center orange">RE-PRINT LABELS</div>';
	hide('plating_return');
	show('plating_print_labels');
	console.log('reprint_plating_labels');
	console.log(plating_item);
	openPage('m_plating_sched', this, 'red','m_modal','tabclass');
}
function do_show_menu_item_components(menu_item_id)
{
	set_barcode_mode("PT_comp");
	openPage('m_plating_sched', this, 'red','m_modal','tabclass');
	active_menu_item_id = menu_item_id; // global - so we can come back to it
	// var div = document.getElementById('menu_item_components_div');
	plating_item = find_plating_item(menu_item_id);
	console.log(plating_item);
	if (plating_item == null) { // 
		console.log("ERROR do_show_menu_item_components");
		// plating_item = Object.create(get_menu_item_by_id(menu_item_id)); // possibly dangerous .....

	}
	var div = document.getElementById('plating_sched_list');
	div.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'item_table';
	var tr = document.createElement('tr');
	var th = document.createElement('th');
	th.innerHTML= margin(plating_item.dish_name + "<br>" + plating_item.code);
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML=margin('S/L');
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML=margin('TEMP');
	tr.appendChild(th);
	tab.appendChild(tr);
	var line = 1;
	var all_good = true; // check before useby date and temp measured ok
	if (plating_item != null) {
		console.log("found menu_item ",plating_item.dish_name,plating_item.items.length);
		console.log(plating_item);
		console.log(JSON.stringify(plating_item));
		var items = plating_item.items;	
		for (var i = 0; i < items.length; i++) {
			
				console.log("found ",items[i].description);
				var tr = document.createElement('tr');
				// tr.appendChild(new_td(line++,'item'));
				var clickdiv = "<div onclick='XXplating_comp_selected(" + i + ");'>" + items[i].description + "</div>";
				// show items in coolroom ready to be plated
			 	//clickdiv += show_plating_comps(items[i].description);
			//	tr.appendChild(new_td(items[i].description,'item'));
				tr.appendChild(new_td(clickdiv,'item'));
				var td = document.createElement('td');
				td.id = 'plating_item_checked_' + i;
				td.innerHTML = '-';
				if (items[i].component_id) {
					td.innerHTML = '&#x2713;';
				}
				else {
					all_good = false;
				}
				tr.appendChild(td);
				var td = document.createElement('td');
				td.id = 'plating_item_temp_' + i;
				td.innerHTML = '-';
				if (items[i].M1_temp) {
					td.innerHTML = items[i].M1_temp;
				}
				else {
					all_good = false;
				}
				tr.appendChild(td);
				tab.appendChild(tr);
			
		}
		div.appendChild(tab);
		if (all_good) {
			hide('plating_return');
			show('plating_print_labels');
			plating_item.checked = true;
		}
		else {
			show('plating_return');
			hide('plating_print_labels');
			plating_item.checked = false;
		}
	}
}

function inc_labels(div_id,inc,min,max)
{
	console.log("inc_labels",div_id,inc);
	var div = document.getElementById(div_id);
	var val = parseInt(div.innerHTML);
	val = parseInt(val + inc);
	if (val < min) val = min;
	if (val > max) val = max;
	div.innerHTML = val;
}

function goto_comp_search()
{
	load_comps();
	$('#search').val('');
	document.getElementById('new_comp_btns').style.display = 'none';
	openPage('m_search', this, 'red','m_modal','tabclass');
	hide('kitchen_manual_code');
}

function new_component() {
	console.log('adding ' + $('#search').val());
	var component = new Object();
	component.description = $('#search').val();
	
	// component.prep_type = new_comp['prep_type'];
	
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    $.ajax({
        url: RESTHOME + "new_component.php",
        type: "POST",
        data: data,

        success: function(result) { 
        	load_comps(component_selected);
        	// component_selected();
        },
        fail: (function (result) {
            console.log("new _component fail ",result);
        })
    });
}

function show_dock_component(cid)
{
	show('dock_display_comp_div');
	var div = document.getElementById('dock_display_comp_div1');
	div.innerHTML = '';
	new_comp = get_component_by_id(cid);
	if (!new_comp) {
		alert("ERROR");
		return;
	}
	console.log(new_comp);
	var flds = ['description','supplier','product','spec','shelf_life_days'];
	for (var i =0; i < flds.length; i++) {
		var d = document.createElement('div');
		d.className = 'smaller';
		d.innerHTML = flds[i] + ":";
		div.appendChild(d);
		var d = document.createElement('div');
		d.className = 'small';
		if (new_comp[flds[i]] == null) {
			d.innerHTML = "NOT SET";
		}
		else {
			d.innerHTML = new_comp[flds[i]];
		}
		
		div.appendChild(d);
	}
	// show preptype details
	var ptid = new_comp['prep_type'];

	var d = document.createElement('div');
	d.className = 'smaller';
	d.innerHTML = "Prep Type:";
	div.appendChild(d);
	var d = document.createElement('div');
	d.className = 'small';
	d.innerHTML = get_preptype_val(ptid,'code');
	
	div.appendChild(d);
	show('dock_comp_selected_btns');
}

function show_dock()
{
	openPage('dock_main', null, 'red','mobile_main','tabclass');
	openPage('m_dock',document.getElementById('supplier_list_tab'), 'red','m_modal','tabclass');
	var dock_items = new Array();
	var div = document.getElementById('dock_display_comp_div1');
	div.innerHTML = '';
	var table = document.createElement('table');
	table.width = '100%';
	for (var i = 0; i < comps.length;i++) {
		if (comps[i].high_risk == 1) {
			var tr = document.createElement('tr');
			var td = document.createElement('td');
			var innerHTML = "<div onclick='show_dock_component(" + comps[i]['id'] + ");'>" + comps[i]['description'] + "</div>";
			td.innerHTML = innerHTML;
			tr.appendChild(td);
			var td = document.createElement('td');
			td.innerHTML = comps[i]['supplier'];
			tr.appendChild(td);
			table.appendChild(tr);
			console.log(comps[i]);
			dock_items.push(comps[i]);
		}
	}
	div.appendChild(table);
	setup_dock_search(dock_items);
}
function setup_dock_search(dock_items)
{
	 $('#dock_search').autocomplete({
         // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
         minLength: 2,
 		source: dock_items,
 		response: function( event, ui ) { 
 			// console.log("search response found " + ui.content.length); console.log(ui);
 			/*
 			if (ui.content.length == 0) {
 				document.getElementById('new_comp_btns').style.display = 'block';
 			}
 			else {
 				document.getElementById('new_comp_btns').style.display = 'none';
 			} */
 		},
 		select: function(event, ui) {
             // place the person.given_name value into the textfield called 'select_origin'...
             $('#dock_search').val(ui.item.label);
             // and place the person.id into the hidden textfield called 'link_origin_id'. 
          	console.log('selected ',ui.item.value);
          	show_dock_component(ui.item.value);
          	// cordova.plugins.Keyboard.close();
             return false;
         }  
     })
}

function goto_dock()
{
	load_comps(show_dock);
}
function goto_m_main(new_mode)
{
	if (new_mode) mode = new_mode;
	if (mode == 'kitchen') {
		openPage('mm2', document.getElementById('m_current_tracking_tab'), 'red','mobile_main','tabclass');
		m_tracking();
	}
	else if (mode == 'dock') {
		openPage('dock_main', document.getElementById('m_plating_team_tab'), 'red','mobile_main','tabclass');
		
	}
	else {
		goto_plating();
	}
}

function get_preptype_val(id,fld)
{
	for (var i = 0; i < preptypes.length; i++) {
		if (preptypes[i].id == id) {
			return(preptypes[i][fld]);
		}
	}
	return("not found");
}


function draw_ingredients() // returns true if all ingredients are selected and have a temperature
{
	var finished = true;
	openPage('m_temp_modal1', this, 'red','m_modal2','tabclass');
	div = document.getElementById('m1_temp_div_1');
	var d = "<div class='margin10'><table width='100%'>";
	d += "<tr><td>Description</td><td>ID</td><td>Temperature</td></tr>";
	for (var i = 0; i < new_comp['selected_ingredients'].length; i++) {
		var sub = get_component_by_id(new_comp['selected_ingredients'][i]['id']);
		d += "<tr><td>" + sub['description'] + '</td>';
		if (new_comp['selected_ingredients'][i]['cid']) {
			d += "<td>" + new_comp['selected_ingredients'][i]['cid'] + "</td>";
		}
		else {
			finished = false;
			d += "<td>-</td>";
		}
		if (new_comp['selected_ingredients'][i]['temp']) {
			d += "<td>" + new_comp['selected_ingredients'][i]['temp'] + "</td>";
		}
		else {
			d += "<td>-</td>";
			finished = false;
		}
		
		d += "</tr>";
	}
	d += '</table></div>';
	div.innerHTML = d;
	return(finished);
}
// called when user searchs for and selects a component - M1 only 


function component_selected(id)
{
	console.log("component selected - loading chefs");
	load_chefs(null);
	new_comp == null;
	active_comp = null;
	if (id) {
		new_comp = get_component_by_id(id);
	}
	console.log(new_comp);
	if (!new_comp) {
		console.log("can't find component - search for " + $('#search').val());
		new_comp = get_component_by_description($('#search').val());
	}
	if (new_comp['prep_type'] < 1) new_comp['prep_type'] = 1;
	console.log(new_comp);
	var prep_type_id = new_comp['prep_type'];
	console.log('prep_type_id',prep_type_id);
	if (prep_type_id < 1) prep_type_id = 1;
	
	var M1_temp = get_preptype_val(prep_type_id,'M1_temp');
	var prep_type_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	new_comp.shelf_life_days = get_preptype_val(prep_type_id,'shelf_life_days');
	var sign = ' > ';
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	if (prep_type_sign == 0) {
		sign = ' < ';
	}
	console.log(new_comp);
	console.log(new_comp['subcomponents']);
	// subcomponents is an array of ids - needs to become an array of objects to store temperature and used id
	
	if (new_comp['subcomponents']) {
		if (!new_comp['selected_ingredients']) {
			new_comp['selected_ingredients'] = new Array();
			for (var i = 0; i < new_comp['subcomponents'].length; i++) {
				new_comp['selected_ingredients'][i] = new Object();
				new_comp['selected_ingredients'][i]['id'] = new_comp['subcomponents'][i];
			}
		}
		console.log('ingredients');
		set_barcode_mode('scan_ingredients');
		draw_ingredients();
		
	}
	else if (M1_temp == null) { // low risk. No temp required
		console.log("LOW RISK");
		set_barcode_mode("M1_LR");
		openPage('m_temp_modal_LR', this, 'red','m_modal2','tabclass');
		document.getElementById('m1_temp_div_LR_comp').innerHTML = new_comp['description'];
		document.getElementById('ms_2').innerHTML = ' ';
		document.getElementById('ms_2_text').innerHTML = ' ';
		document.getElementById('ms_2_target').innerHTML = "";
		document.getElementById('chk_temp_item_div').innerHTML = '';
	}
	else {
		set_barcode_mode("M1");
		set_temp_mode("M1");
		openPage('m_temp_modal', this, 'red','m_modal2','tabclass');
		document.getElementById('ms_2').innerHTML = 'M1';
		document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
		document.getElementById('ms_2_target').innerHTML = sign + get_preptype_val(prep_type_id,'M1_temp') + "&#176";
		document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];
	}
	// openPage('m_temp_modal', this, 'red','m_modal2','tabclass');
	// document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];
	document.getElementById('ms_1').innerHTML = '';
	document.getElementById('ms_1_text').innerHTML = '';
	

	document.getElementById('chk_temp_pt_div').innerHTML = get_preptype_val(prep_type_id,'code');
}

function dock_read_M1temp(callback)
{
	console.log('dock_read_M1temp');
	console.log(active_comp);
	load_chefs(null);
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	document.getElementById('dock_m1_temp_div').innerHTML = '';
	show('dock_m_temp_modal');
	read_temp('M1_dock');
}

function read_M1temp(callback){
	load_chefs(null);
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	document.getElementById('m1_temp_div').innerHTML = '';
	read_temp('M1');
}

function read_M2temp(callback){
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	document.getElementById('m1_temp_div').innerHTML = '';
	read_temp('M2');
}
function read_pt_M2temp(callback){
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	document.getElementById('m1_temp_div').innerHTML = '';
	read_temp('M2_plating');
}
function read_plating_M1temp(callback){
	
	// document.getElementById('m1_temp_div').innerHTML = '';
	read_temp('M1_plating');
}

function check_temp_m1_dock(t)
{
	console.log("check temp dock",t);
	new_comp.M1_temp = t; // 
	
	console.log(new_comp);
	
	// var t = document.getElementsByName('m1_temp')[0].value;
	var prep_type_id = new_comp['prep_type']; // should always be 6,7 or 8 (DOCK)
	var M1_temp_target = get_preptype_val(prep_type_id,'M1_temp');
	var M1_temp_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	document.getElementById('dock_m1_temp_div').innerHTML = '';
// 	document.getElementById('dock_m1_temp_div_2').innerHTML=parseInt(t) + "&#176C"
	//document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C"
	document.getElementById('dock_m1_temp_div_4').innerHTML= parseInt(t * 10) / 10 + "&#176C"
	document.getElementById('dock_m1_temp_div_6').innerHTML= parseInt(t * 10) / 10 + "&#176C"
	console.log("check temp",t,M1_temp_target);
	if (t.length > 0) {
		if (M1_temp_sign == 1) {// should never happen
			alert("incorrect prep type");
		}
		else { 
			if (parseInt(t) > parseInt(M1_temp_target)) {
				console.log("DOCK M1 temp too high");
				openPage('dock_m_temp_modal_high', this, 'red','m_modal','tabclass');
			}
			else {
				openPage('dock_m_temp_modal_labels', this, 'red','m_modal','tabclass');
			}
		}
	}
	
}

function dock_QA_scan(uid)
{
	console.log('dock_QA_scan(uid) ' + uid);
	new_comp.M1_action_code = 10; // TODO must fix
	new_comp.M1_action_id = uid;
	var chef = get_chef_by_id(uid);
	if (chef) {
		document.getElementById('dock_qa_chef_details').innerHTML = chef.label;
		openPage('dock_m_temp_modal_qa2', this, 'red','m_modal','tabclass');
	}
	
	// openPage('dock_m_temp_modal_labels', this, 'red','m_modal','tabclass');
}

function dock_qa_client_request()
{
	new_comp.M1_action_code = 11; // TODO must fix
	openPage('dock_m_temp_modal_labels', this, 'red','m_modal','tabclass');
}

function dock_qa_signof()
{
	new_comp.M1_action_code = 10; // TODO must fix
	openPage('dock_m_temp_modal_labels', this, 'red','m_modal','tabclass');
}

function dock_qa_override()
{
	set_barcode_mode('dock_QA');
	openPage('dock_m_temp_modal_qa', this, 'red','m_modal','tabclass');
}

function check_temp(t) // start a new component
{
	console.log("check temp",t);
	new_comp.M1_temp = t; // 
	
	console.log(new_comp);
	
	// var t = document.getElementsByName('m1_temp')[0].value;
	var prep_type_id = new_comp['prep_type'];
	var M1_temp_target = get_preptype_val(prep_type_id,'M1_temp');
	var M1_temp_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	document.getElementById('m1_temp_div').innerHTML = '';
	document.getElementById('m1_temp_div_2').innerHTML=parseInt(t) + "&#176C"
	document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C"
	document.getElementById('m1_temp_div_4').innerHTML=parseInt(t) + "&#176C"
	console.log("check temp",t,M1_temp_target);
	if (t.length > 0) {
		if (M1_temp_sign == 1) {
			if (parseInt(t) < parseInt(M1_temp_target)) {
				console.log("M1 temp too low");
				openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
			}
			else {
				openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			}
		}
		else {
			if (parseInt(t) > parseInt(M1_temp_target)) {
				console.log("M1 temp too high");
				openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
			}
			else {
				openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			}
		}
	}
	
}

function add_chef_select(target_div,input_name) 
{
	var s = document.getElementById(target_div);
	s.innerHTML = null;
	var select = document.createElement('select');
	select.name = input_name;
	// console.log('found plating teams ',plating_teams.length);
	for (var i = 0; i < chefs.length; i++) {
		option = document.createElement( 'option' );
		option.value = chefs[i]['id'];
		option.textContent =  chefs[i]['label'];
		select.appendChild( option );
		// console.log(i);
	}
	s.appendChild(select);
	
}

function dock_start_component()
{
	start_component(true);
}

function start_component(dock)
{
	// object copy is messy - TODO
	load_chefs(add_chef_select('m1_temp_div_chef','m1_chef_id'));
	console.log('start component');
	console.log(active_comp);
	console.log(new_comp);
	// check if component at M0 - has ingredients
	if (!new_comp || (active_comp && active_comp['selected_ingredients'])) {
		console.log('component start - need to print labels');
		comp_milestone(active_comp['M1_temp']);
        goto_m_main();
		return;
	}
	var component = new Object();
	component.description = new_comp['description']; // simplifies display
	active_comp = copy_object(new_comp);
	component.comp_id = new_comp['id'];
	component.prep_type = new_comp['prep_type'];
	component.M1_action_code = new_comp['M1_action_code'];
	component.M1_action_id = new_comp['M1_action_id'];
	component.shelf_life_days = new_comp.shelf_life_days;
	component.items = new_comp.selected_ingredients;
	component.dock = dock;
	prep_type_id = component.prep_type;
	console.log("start compontent " + component.description + " prep_type" + component.prep_type);
	// component.M1_temp = document.getElementsByName('m1_temp')[0].value;
	var M2_time = get_preptype_val(prep_type_id,'M2_time_minutes');
	console.log("At M1, M2 time = " + M2_time);
	console.log("At M1, M3 time = " + get_preptype_val(prep_type_id,'M3_time_minutes'));
	if (new_comp['M1_temp']) component.M1_temp = new_comp['M1_temp'];
	if (M2_time == null) {
		component.finished = 'true';
		
	}
	else {
		component.M1_temp = new_comp['M1_temp'];
	}
	if (!dock) {
		component.M1_chef_id = document.getElementsByName('m1_chef_id')[0].value;
	
		if (component.M1_chef_id < 1) component.M1_chef_id = 1;
		 document.getElementsByName('m1_chef_id')[0].value = '';
	}
	else {
		component.M1_chef_id = 0;
	}
	var data =  {data: JSON.stringify(component)};
    console.log("start_component Sent Off: ", data);
    var qty_input = (dock)?'dock_m1_label_qty':'m1_label_qty';
   //  document.getElementsByName('m1_temp')[0].value = '';
    $.ajax({
        url: RESTHOME + "new_comp.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("start_component result ",result);
            var comp = JSON.parse(result);
            console.log("start_component id =  ",comp.id);
            var qty = document.getElementsByName(qty_input)[0].value;
            active_comp.id = comp.id;
            active_comp.expiry_date = comp.expiry_date;
            print_component_labels(qty);
            document.getElementsByName(qty_input)[0].value = 1;
            console.log('comp.dock = ',comp.dock);
            if (comp.dock == true) goto_dock()
            else goto_m_main();
            new_comp = null;
        },
        done: function(result) {
            console.log("done start_component result ",result);
        },
        fail: (function (result) {
            console.log("start_component fail ",result);
        })
    });
    
}

function set_user(input_name,next_page,uid) {
	if (uid == 0) {
		uid = document.getElementsByName(input_name)[0].value;
	}
	else {
		document.getElementsByName(input_name)[0].value = uid;
	}
	console.log("got user id ",uid);
	// openPage(next_page, this, 'red','m_modal2','tabclass');
/*	if (uid.substring(0,1) == 'u') {
		uid = parseInt(uid.substring(4));
		console.log("parsed user id ",uid);
		document.getElementsByName(input_name)[0].value = uid;
	} */
	var chef = get_chef_by_id(uid);
	if (chef) {
	    console.log("found chef ",chef['label']); 
	    document.getElementById('m1_temp_div_5').innerHTML = chef['label'];
	    openPage(next_page, this, 'red','m_modal2','tabclass');
	}
		
	
}
function comp_milestone(temp_reading)
{
	// send data to REST interface
	console.log('comp_milestone');
	console.log(active_comp);
	var prep_type_id = active_comp['prep_type_id'];
	document.getElementById('dock_m1_temp_div_4').innerHTML= parseInt(temp_reading * 10) / 10 + "&#176C"
	var temp_target = get_preptype_val(prep_type_id,'M2_temp');
		
	var component = new Object();
	component.id = active_comp['id'];
	var url = '';
	if (active_comp['M1_time'] == '') { // M1
		// component.M2_temp = document.getElementsByName('m2_temp')[0].value;
		component.M1_temp = temp_reading;
		component.M1_chef_id = 0; // TODO
		
		url = RESTHOME + 'M1_comp.php';
	}
	else if (active_comp['M2_time'] == '') { // M2
		// component.M2_temp = document.getElementsByName('m2_temp')[0].value;
		component.M2_temp = temp_reading;
		active_comp['M2_time'] = 'now';
		component.M2_chef_id = 0;
		var M3_time_minutes = get_preptype_val(prep_type_id,'M3_time_minutes');
		console.log("At M2, M3 time = " + M3_time_minutes + " ->" + typeof(M3_time_minutes));
		if (M3_time_minutes == null) {
			console.log("component finished");
			component.finished = 'true';
		}
		url = RESTHOME + 'M2_comp.php';
	}
	else {
		//component.M3_temp = document.getElementsByName('m2_temp')[0].value;
		component.M3_temp = temp_reading;
		active_comp['M3_time'] = 'now';
		component.M3_chef_id = 0;
		url = RESTHOME + 'M3_comp.php';
	}
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    console.log('to ' + url);
    $.ajax({
        url: url,
        type: "POST",
        data: data,

        success: function(result) {
            console.log("comp_milestone result ",result);
            console.log(active_comp);
            if (active_comp['M2_time'] == '') { 
            	// at M1 - component has tracked ingredients
            	// get chef id and print labels
            	console.log("At M1");
            	var qty = document.getElementsByName('m1_label_qty')[0].value;
                print_component_labels(qty);
                document.getElementsByName('m1_label_qty')[0].value = 1;
          //  	openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
            }
            else {
            	if (active_comp['M3_time'] != '') {
            		console.log('finished');
            		openPage('m2_temp_modal3', this, 'red','m_modal2','tabclass');
            	}
            	else {
            		console.log('at M2');
            	}
            }
        },
        done: function(result) {
            console.log("done start_component result ",result);
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
	        })
    });
}

function check_temp_m2(t) // M2 or M3 .... or M1 if component has ingredients.
{
	// if M1 then need to print labels and log temperature to existing record TODO
	console.log("check temp M2/3");
	console.log(active_comp);
	
	// var t = document.getElementsByName('m2_temp')[0].value;
	console.log("check temp",t);
	if (t.length > 0) {
		openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
		var prep_type_id = active_comp['prep_type_id'];
		
		var temp_target = get_preptype_val(prep_type_id,'M1_temp');
		var milestone = 'M1';
		if (active_comp['M1_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M2_temp');
			milestone = 'M2';
		} 
		if (active_comp['M2_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M3_temp');
			milestone = 'M3';
		} 
		console.log('check_temp_m2 target temp',temp_target,t,milestone);
		document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C"
		document.getElementById('m1_temp_div_4').innerHTML=parseInt(t) + "&#176C"
		document.getElementById('m2_temp_div_2').innerHTML=parseInt(t) + "&#176C"
		document.getElementById('m2_temp_div_3').innerHTML=parseInt(t) + "&#176C"
	//	document.getElementById('dock_m1_temp_div_3').innerHTML= parseInt(t * 10) / 10 + "&#176C";
		document.getElementById('dock_m1_temp_div_4').innerHTML= parseInt(t * 10) / 10 + "&#176C";
		if (milestone == 'M1' && parseInt(t) > parseInt(temp_target)) {
			document.getElementById('m2_temp_div_2a').innerHTML= milestone + " achieved";
			document.getElementById('m2_temp_div_3a').innerHTML= milestone + " achieved";
			active_comp['M1_temp'] = t;
			openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			return;
			// comp_milestone(t);
		}
		if (parseInt(t) < parseInt(temp_target)) {
			document.getElementById('m2_temp_div_2a').innerHTML= milestone + " achieved";
			document.getElementById('m2_temp_div_3a').innerHTML= milestone + " achieved";
			comp_milestone(t);
		}
		else {
			openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			// document.getElementById('m2_temp_div_2a').innerHTML= milestone + "";
			document.getElementById('m2_temp_div_2a').innerHTML= milestone + " not achieved";
			document.getElementById('m2_temp_div_3a').innerHTML= milestone + " not achieved";
			document.getElementById('m2_temp_div_2').innerHTML= "<div class='red'>" + parseInt(t) + "&#176C</div>"
		}
		openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
	}
	
}


function active_comp_selected(id) 
{
	console.log("active_comp_selected",id);
	load_chefs(null);
	openPage('m2_temp_modal', this, 'red','m_modal2','tabclass');
	active_comp = active_comps[id];
	console.log(active_comp);
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	
	var prep_type_id = active_comp['prep_type_id'];
	console.log('prep_type_id',prep_type_id);
	if (prep_type_id < 1) prep_type_id = 1;
	var prep_type_val = get_preptype_val(prep_type_id,'M2_temp');
	document.getElementById('chk_temp_item_div').innerHTML = active_comp['description'];
	var milestone_due = 'NA';
	var remaining = 0;
	var milestone_temp = "NA";
	var target_temp = "NA";
	var M1_time = new Date(active_comp['M1_time']);
	var M2_time = new Date(active_comp['M2_time']);

	console.log("M2 time -",active_comp['M2_time'],"-");
	var remaining = 0;
	var now = new Date();
	var now_ms = now.getTime();
	var M1_ms = M1_time.getTime(); // time in millisecs
	if (active_comp['M1_time'] == '') {
		milestone_due = 'M1';
		target_temp = " > " + get_preptype_val(prep_type_id,'M1_temp');
	}
	else if (active_comp['M2_time'] == '') {
		milestone_due = 'M2';
		var M2_due_min = get_preptype_val(prep_type_id,'M2_time_minutes');
		var M2_due_ms = M1_ms + M2_due_min * 60 * 1000;  			
		remaining = (M2_due_ms - now_ms) / (60 * 1000);
		console.log("M2_due_min M1_ms",M2_due_min,M1_ms,M2_due_ms,format_minutes(remaining));
		target_temp = " < " + get_preptype_val(prep_type_id,'M2_temp');
		}
	else {
		milestone_due = 'M3';
		var M3_due_min = get_preptype_val(prep_type_id,'M3_time_minutes');
		var M3_due_ms = M1_ms + M3_due_min * 60 * 1000;  			
		remaining = (M3_due_ms - now_ms) / (60 * 1000);
		console.log("M3_due_min M1_ms",M3_due_min,M1_ms,M3_due_ms,format_minutes(remaining));
		target_temp = " < " + get_preptype_val(prep_type_id,'M3_temp');
	}
	document.getElementById('ms_1').innerHTML = milestone_due;
	if (milestone_due != 'M1') {
		if (remaining >= 0) {
			document.getElementById('ms_1_text').innerHTML = format_minutes(remaining) + " REMAINING";
		}
		else {
			document.getElementById('ms_1_text').innerHTML = format_minutes(remaining) + " OVERDUE";
		}
	}
	else {
		document.getElementById('ms_1_text').innerHTML = "";
	}
	document.getElementById('ms_2').innerHTML = milestone_due;
	document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
	document.getElementById('ms_2_target').innerHTML = target_temp + "&#176;";

	document.getElementById('chk_temp_pt_div').innerHTML = get_preptype_val(prep_type_id,'code');
}


function reprint_comp_labels()
{
	console.log('reprint_comp_labels');
	openPage('m_reprint_labels', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	show('kitchen_manual_code');
	set_barcode_mode('kitchen_reprint');
	load_reprint_data();
	load_chefs();
}
function load_reprint_data()
{
	
	 $.ajax({
	        url: RESTHOME + "get_active_comps.php?all=true",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	     //        console.log("got " + result.length + " comps");
	            m_show_active_components(result,true);
	            
	        },
	        done: function(result) {
	            console.log("done load_comps ");
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}


function m_tracking()
{
	console.log('goto_active_components');
	openPage('m_current_tracking', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	show('kitchen_manual_code');
	set_barcode_mode('active_comp');
	load_tracking_data();
}

function load_tracking_data()
{
	 $.ajax({
	        url: RESTHOME + "get_active_comps.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            // console.log("got " + result.length + " comps");
	            m_show_active_components(result,false);
	            
	        },
	        done: function(result) {
	            console.log("done load_comps ");
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}

function get_comps_for_plating(item)
{
	console.log('get_comps_for_plating');
	 $.ajax({
	        url: RESTHOME + "get_active_comps.php?finished=true",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            plating_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            console.log("got " + result.length + " comps for plating");
	            // m_show_active_components(result);
	            do_show_menu_item_components(item);
	        },
	        done: function(result) {
	            console.log("done get_comps_for_plating");
	        },
	        fail: (function (result) {
	            console.log("fail get_comps_for_plating",result);
	        })
	    });
}


function load_plating_teams()
{
	
	 $.ajax({
	        url: RESTHOME + "get_plating_teams.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	           //  active_comps = result;
	           // clear plating teams;
	        	if (plating_teams != null) {
		        	for (var i = 0; i < plating_teams.length; i++ ) {
		        		if (plating_teams[i] != null) plating_teams[i] = [];
		        	}
	        	
		            console.log("got " + result.length + " plating_teams");
		            for (var i = 0; i < result.length;i++) {
		            	console.log("pt",result[i].user_id,result[i].team_id);
		            	var chef = get_chef_by_id(result[i].user_id);
		            	if (chef) {
		            		console.log("found chef ",chef['label']);
		            		if (plating_teams[result[i].team_id] == null) {
		            			plating_teams[result[i].team_id] = [];
		            		}
		            		plating_teams[result[i].team_id].push(chef);
		            	}
		            	else {
		            		console.log("could not find chef");
		            	}
		            	//plating_teams[result[i].team_id].push(get_chef_by_id(result[i].user_id));
		            	// plating_teams[active_plating_team].push(chefs[idx]);
		            }
	        	}
	       
	            
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}

function format_minutes (min)
{
	min = Math.abs(min)
	 var hours   = Math.floor(min / 60);
	 var minutes = Math.floor(min - (hours * 60));
	 if (hours   < 10) {hours   = "0"+hours;}
	 if (minutes < 10) {minutes = "0"+minutes;}
	 if (parseInt(hours) == 0) return(minutes + " minutes");
	 return hours+':'+minutes;
}

function clear_comp_fields ()
{
	document.getElementById('ms_1').innerHTML = '';
	document.getElementById('ms_1_text').innerHTML = '';
	document.getElementById('ms_2').innerHTML = '';
	document.getElementById('ms_2_text').innerHTML = '';
	document.getElementById('ms_2_target').innerHTML = '';
}

function reprint_labels()
{
	var qty = document.getElementsByName('m1_reprint_label_qty')[0].value;
	if (qty && qty > 0) {
		print_component_labels(qty);
		goto_m_main();
	}
	
}

function print_component_labels(qty)
{
	console.log(" print_component_labels ",qty);

	var comp = Object.assign({}, active_comp);
	comp.copies = qty;
	if (!comp.preparedBy) {
		console.log(comp);
		var chef = get_chef_by_id(comp['M1_chef_id']);
		if (chef) {
		    console.log("found chef ",chef['label']); 
		    comp.preparedBy = chef['label'];
		}
		else {
			comp.preparedBy = 'DOCK';
		}
	}
	
	var data =  {data: JSON.stringify(comp)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: RESTHOME + "comp_label.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log("comp_label result ",result);
            
        },
        
        fail: (function (result) {
            console.log("comp_label fail ",result);
        })
    });
	// goto_m_main();
}

// TODO - come up with a sensible naming system for groups of functions
function reprint_supplier_labels()
{
	console.log('reprint_supplier_labels');
	set_barcode_mode('dock_reprint'); // callback to reprint_doc_labels
	
}

function reprint_dock_labels(cid)
{
	console.log('reprint_dock_labels',cid);
	load_chefs(null);
	document.getElementById('drl_details_div').innerHTML = cid;
	// set_barcode_mode('dock_reprint');
	$.ajax({
        url: RESTHOME + "get_active_comps.php?cid=" + cid,
        type: "POST",

        success: function(result) {
        	console.log(result);
        	
        	var comps = JSON.parse(result);
            if (comps) {
            	active_comp = comps[0];
            	document.getElementById('drl_details_div').innerHTML = active_comp.description;
            	openPage('m_dock_reprint1', document.getElementById('s_reprint_labels_tab'), 'red','m_modal','m_top_menu',null);
            	console.log("got component " + active_comp.description);
            }
            else {
            	console.log('could not find incredient')
            	set_barcode_mode('dock_reprint');
            }
            
        },
        fail: (function (result) {
            console.log("fail check_ingredient ",result);
        })
    });
	// dock_start_component
}

function dock_reprint()
{
	if (!active_comp) {
		console.log('dock_reprint ERROR - no active component');
		return;
	}
	qty = document.getElementById('dock_reprint_label_qty').value;
	if (qty > 0 && qty < 200) {
		openPage('m_dock_reprint', document.getElementById('s_reprint_labels_tab'), 'red','m_modal','m_top_menu',null);
		print_component_labels(qty);
	}
	else {
		console.log('dock_reprint invalid qty')
	}
}

function reprint_active_comp_labels(id)
{
	active_comp = active_comps[id];
	clear_comp_fields();
	document.getElementById('chk_temp_item_div').innerHTML = active_comp['description'];
	openPage('m_reprint_modal4', this, 'red','m_modal2','tabclass');
	openPage('m_temp', this, 'red','mobile_main','tabclass');
}

function checkTime(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function display_real_time()
{
	//var div = document.getElementById('');
	var today = new Date();
	  var h = today.getHours();
	  var m = today.getMinutes();
	  
	  // add a zero in front of numbers<10
	  m = checkTime(m);
	  h = checkTime(h);
	  document.getElementById('current_time_kitchen').innerHTML = h + ":" + m;
	  document.getElementById('current_time_dock').innerHTML = h + ":" + m;
}
function m_show_active_components(data,reprint)
{
	display_real_time();
	var div = document.getElementById('m_current_tracking');
	if (reprint) div = document.getElementById('m_reprint_labels');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	if (reprint) {
		div.innerHTML = "<h1>Reprint Labels</h1>";
	}
	else {
		div.innerHTML = "<h1>Active Components</h1>";
	}
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	
	
    tr.appendChild(new_td('Description','comp'));  
    if (!reprint) {
    	tr.appendChild(new_td('M','comp'));
    
    	tr.appendChild(new_td('TIME','comp'));
    }
   	tab.appendChild(tr);
   	for (var i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		var span_txt = "<span class='hidden'>" + data[i]['id'] + "</span>";
   		var clickdiv = "<div class='tooltip' onclick='active_comp_selected(" + i + ");'>" + data[i]['description'] + span_txt + "</div>";
   		if (reprint) clickdiv = "<div onclick='reprint_active_comp_labels(" + i + ");'>" + data[i]['description'] + "</div>";
   		// tr.appendChild(new_td(data[i]['description'],'comp'));
   		tr.appendChild(new_td(clickdiv,'comp'));
   		
   		var M1_time = new Date(data[i]['M1_time']);
   		var M2_time = new Date(data[i]['M2_time']);
   		var prep_type_id = data[i]['prep_type_id'];
   	//	console.log("M2 time -",data[i]['M2_time'],"-");
   		var remaining = 0;
   		var now = new Date();
		var now_ms = now.getTime();
		var M1_ms = M1_time.getTime(); // time in millisecs
	//	console.log("prep_type_id",prep_type_id);
		if (reprint) {
   			
   		}
		else if (data[i]['M1_time'] == '') { // M0 - ingredients have been selected
			tr.appendChild(new_td('.','comp'));
			tr.appendChild(new_td('Cooking','comp'));
		}
   		else {
	   		if (data[i]['M2_time'] == '') {
	   			var M2_due_min = get_preptype_val(prep_type_id,'M2_time_minutes');
	   			var M2_due_ms = M1_ms + M2_due_min * 60 * 1000;  			
	   			remaining = (M2_due_ms - now_ms) / (60 * 1000);
	//   			console.log("M2_due_min M1_ms",M2_due_min,M1_ms,M2_due_ms,format_minutes(remaining));
	   			tr.appendChild(new_td('<div class="m_bluedot">2</div>','comp'));
	   		}
	   		else {
	   			var M3_due_min = get_preptype_val(prep_type_id,'M3_time_minutes');  			
	   			var M3_due_ms = M1_ms + M3_due_min * 60 * 1000; 			
	   			remaining = (M3_due_ms - now_ms) / (60 * 1000);
	   			tr.appendChild(new_td('<div class="m_bluedot">3</div>','comp'));
	   		}
   		// var M1_t = M1_time.getHours() + ":" + M1_time.getMinutes();
   		
	   		if (remaining > 0) {
	   			tr.appendChild(new_td(format_minutes(remaining) + " remaining",'comp'));
	   		}
	   		else {
	   			var td = new_td(format_minutes(Math.abs(remaining)) + " overdue",'comp red');
	   			
	   			tr.appendChild(td);
	   		}
   		}
   		// tr.appendChild(new_td(data[i]['M1_time'],'comp'));
   		
   		  		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}

function find_plating_teams(menu_items)
{
	console.log('searching for assigned plating teams ',menu_items.length);
	
	if (plating_teams == null) plating_teams = [];
	for (var i = 0; i < menu_items.length; i++) {
		// console.log("item ",menu_items[i]['code'],menu_items[i]['plating_team']);
		if (menu_items[i]['plating_team'] != '') {
			console.log("item ",menu_items[i]['code'],menu_items[i]['plating_team']);
			var pt = menu_items[i]['plating_team'];
			if (typeof plating_teams[pt] == 'undefined') {
				plating_teams[menu_items[i]['plating_team']] = [];
			}
		}
	}
	var d = document.getElementById('plating_teams_list');
	d.innerHTML = '';
	var select = document.createElement('select');
	select.name = 'sel_pt';
	console.log('found plating teams ',plating_teams.length);
	for (var i = 0; i < plating_teams.length; i++) {
		if (plating_teams[i]) {
			 option = document.createElement( 'option' );
			 option.value = i;
			 option.textContent =  'Team ' + i;
		    select.appendChild( option );
		}
		d.appendChild(select);
	}
	load_chefs(null);
}



function goto_select_team()
{
	console.log('goto_select_team');
	var s = document.getElementById('sel_team_member');
	s.innerHTML = null;
	var select = document.createElement('select');
	select.name = 'sel_chef';
	// console.log('found plating teams ',plating_teams.length);
	for (var i = 0; i < chefs.length; i++) {
		option = document.createElement( 'option' );
		option.value = i;
		option.textContent =  chefs[i]['label'];
		select.appendChild( option );
	}
	s.appendChild(select);
	
	openPage('m_sel_team_members', this, 'red','m_modal','tabclass');
	show_plating_team();
}

function add_team_member(id)
{
	if (id == -1 ) {
		idx = document.getElementsByName('sel_chef')[0].value;
	}
	else { // idx is the id of the user
		idx = -1;
		for (var i = 0; i < chefs.length; i++) {
			if (chefs[i]['id'] == id) idx = i;
		}
	}
	if (idx < 0) {
		console.log ("add_team_member ERROR invalid idx ",idx);
		return;
	}
	console.log('adding ' + chefs[idx]['value'] + " to plating team " + active_plating_team);
	
	var pt = plating_teams[active_plating_team];
	for (var i = 0; i < pt.length; i++) {
		if (pt[i]['id'] == chefs[idx]['id']) return;
	}
	plating_teams[active_plating_team].team_id = active_plating_team;
	plating_teams[active_plating_team].push(chefs[idx]);
	console.log('members ',pt.length);
	show_plating_team();
}

function rem_pt_mem(team,id) // remove plating team member from plating team
{
	// delete plating_teams[team][id];
	plating_teams[team].splice(id,1);
	show_plating_team();
}
function show_plating_team()
{
	var pt = plating_teams[active_plating_team];
	console.log('show_plating_team',active_plating_team,pt.length);
	var hd = document.getElementById('active_plating_team_head');
	hd.innerHTML = "Plating Team " + active_plating_team;
	var l = document.getElementById('plating_team_list');
	
	l.innerHTML = '';
	for (var i = 0; i < pt.length; i++) {
		if (pt[i]) {
			var d = "<div class='m_label'>" + pt[i]['label'];
			d += "<div class='del' onclick='rem_pt_mem(" + active_plating_team + "," + i + ");'>&#x02A2F;</div>";
			d += "</div>";
			l.innerHTML += d;
		}
	}
}

function select_plating_team()
{
	active_plating_team = document.getElementsByName('sel_pt')[0].value;
	if (active_plating_team >= 0) {
		set_barcode_mode('PT');
		console.log("Team " + active_plating_team);
		load_chefs(goto_select_team);
		document.getElementById('plating_comment_div').innerHTML = "Plating Team " + active_plating_team;
	}
}

function get_plating_item_by_menu_item_id(menu_item_id)
{
	
	if (typeof(plating_items) == 'undefined') return(null);
	for (var i = 0; i < plating_items.length; i++) {
		
		if (plating_items[i].menu_item_id == menu_item_id) return(plating_items[i]);
	}
	return(null);
}

function get_plating_item_by_id(id)
{
	
	if (typeof(plating_items) == 'undefined') return(null);
	for (var i = 0; i < plating_items.length; i++) {
		
		if (plating_items[i].id == id) return(plating_items[i]);
	}
	return(null);
}


function load_menu_items()
{	
	console.log("loading menu items" + RESTHOME + "get_menu_items.php");
    $.ajax({
    	url: RESTHOME + "get_menu_items.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            menu_items = result;
            find_plating_teams(menu_items); // see what plating teams are needed
            console.log("got menu_items" + menu_items.length);
            $('#search_menu').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: menu_items,
        		// Once a value in the drop down list is selected, do the following:
                select: function(event, ui) {
                	
                    // place the person.given_name value into the textfield called 'select_origin'...
                    $('#search_menu').val(ui.item.label);
                    // and place the person.id into the hidden textfield called 'link_origin_id'. 
                 	console.log('selected ',ui.item.value);
                 	show_menu_item_components(ui.item.value);
                    return false;
                }
        	
            })
            console.log("got " + result.length + " menu itemss");
            
        },
        done: function(result) {
            console.log("load_menu_items");
        },
        fail: (function (result) {
            console.log("fail load_menu_items",result);
        })
    });
}


function load_comps(fn)
{
console.log("loading menu item components");
    $.ajax({
        url: RESTHOME + "get_comps.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            comps = result;
            $('#search').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: comps,
        		response: function( event, ui ) { 
        			// console.log("search response found " + ui.content.length); console.log(ui);
        			if (ui.content.length == 0) {
        				document.getElementById('new_comp_btns').style.display = 'block';
        			}
        			else {
        				document.getElementById('new_comp_btns').style.display = 'none';
        			}
        		},
        		select: function(event, ui) {
                    // place the person.given_name value into the textfield called 'select_origin'...
                    $('#search').val(ui.item.label);
                    // and place the person.id into the hidden textfield called 'link_origin_id'. 
                 	console.log('selected ',ui.item.value);
                 	component_selected(ui.item.value);
                 	// cordova.plugins.Keyboard.close();
                 	$('#search').blur();
                    return false;
                }  
            })
            console.log("got " + result.length + " comps");
            if (typeof(fn) == 'function') fn();
        },
        done: function(result) {
            console.log("done load_comps ");
        },
        fail: (function (result) {
            console.log("fail load_comps",result);
        })
    });
}

function new_td(content,classname) {
	var td = document.createElement('td');
	td.className = classname;
	td.innerHTML = "<div class='margin10'>" + content + "</div>";
	return(td);
}

function refresh_times()
{
	// console.log("time!");
	load_tracking_data();
	setTimeout(refresh_times,60 * 1000);
}

function search_suppliers()
{
	console.log('search_suppliers');
}

