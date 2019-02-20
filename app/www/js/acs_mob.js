var default_tab='m_current_tracking_tab';
var PLATING_M1_TEMP = 5.0; // should be set in database????
var comps = null;
var plating_comps = null; // components in cool room
var preptypes = null;
var active_comps = null;

var plating_item = null;
var active_plating_team = 0;
var active_comp = null; // the component currently being worked on
var active_menu_item_id = null;
var new_comp = null; // start a new component - M1


var RESTHOME = SERVER_URL+"acs/REST/";

var barcode_mode = null;
var mode = null; // kitchen or plating
var plating_prep_type = 5; // AHR

function copy_object(o)
{
	return(JSON.parse(JSON.stringify(o)));
}

var serial_started = false;
var logline = 0;

function Xlog(s)
{
	// console.log(s);
	if (logline++ > 10) {
		document.getElementById('log_div').innerHTML = '';
		logline = 0;
	}
	document.getElementById('log_div').innerHTML += s + "<br>";
	document.getElementById('log_div').style.display = 'block';
}

function hide_log()
{
	document.getElementById('log_div').style.display = 'none';
}
function errorCallback(e)
{
	log(e);
	serial_started = false;
}
var str;
var temp_mode = null;
var temp_probe = null;
var button_mode = null; // determines what happens when the qpac button is pressed
var last_temp = null;
var last_error_msg = '';

function set_info(msg)
{
	console.log('set_info',msg);
	last_error_msg = msg;
	document.getElementById('error_msg_div').innerHTML = last_error_msg;
}

function set_temp_mode(new_mode)
{
	temp_mode = new_mode;
	button_mode = 'T';
	
	document.getElementById('button_mode_div').innerHTML = 'T';
	clear_temps();
	qpack_resume();
}

// var temp_callback = null;
function temp_callback(s) // works out where to send the temperature reading
{
	console.log('temp_callback',s,temp_mode);
//	temp_probe = probe;
	if (temp_mode == null) {
		log ('temp_mode not set');
		return;
	}
	show_temp(s);
	last_temp = s;
	if (temp_mode == 'M0') {
		set_ingredient_temp(s);
	}
	if (temp_mode == 'M1') {
		check_temp(s);
	}else if (temp_mode == 'M1_dock') {
		// component.M2_temp = temp_reading;
		check_temp_m1_dock(s);
	}
	else if (temp_mode == 'M2') {
		// component.M2_temp = temp_reading;
		check_temp_m2(s);
	} 
	else if (temp_mode == 'M1_plating') {
		// component.M2_temp = temp_reading;
		set_plating_M1_temp(s);
	} 
	else if (temp_mode == 'M2_plating') {
		// component.M2_temp = temp_reading;
		set_plating_M2_temp(s);
	} 
	else {
		log ("don't know what to do with temperature reading " + temp_mode);
	}
}

function manual_temp_submit(t)
{
	let tag = 'manual_temp_submit: ';
	if (t == null) {
		t = document.getElementsByName('manual_temp')[0].value;
		console.log(tag,'reading temp div ',t);
	}
	console.log(tag, 'working with temp ',t);
	document.getElementById('manual_entry').style.display = 'none';
	temp_callback(t);
}

function manual_barcode_submit(input_name)
{
	var s = document.getElementsByName(input_name)[0].value;
	document.getElementsByName(input_name)[0].value = '';
	console.log('manual_barcode_submit',s,input_name);
	process_barcode(s)
}


var temp_readings = 0;
function ioio_start() {
	qpack_start();
}

function popup_timeout(msg) {
	document.getElementById('popup_time_msg').innerHTML = msg;
	document.getElementById('popup_time_div').style.display = 'block';
}

function popup_error(head,msg) {
	document.getElementById('popup_error_head').innerHTML = head;
	document.getElementById('popup_error_msg').innerHTML = msg;
	document.getElementById('popup_error_div').style.display = 'block';
}

function close_popup(div) {
	document.getElementById(div).style.display = 'none';
}

function popup_manual_temp() {
	document.getElementsByName('manual_temp')[0].value = '';
	temp_readings = 0;
	document.getElementById('manual_entry').style.display = 'block';
}

function read_temp(m)
{
	set_temp_mode(m);
	
	console.log('read temp mode',m);
// 	console.log("typeof(serial)",typeof(serial),typeof(serial.write));
	// if (typeof(serial.write) === 'undefined') {
	if (typeof(serial) == 'undefined') {
		console.log('serial undefined');
		popup_manual_temp();
	}
/*	else {
		// serial.write('R'); arduino
		var t = parseInt(100.0 * p37); //p37 = 0
		log('read temp ' + t);
		temp_callback(t);
	} */
}


function set_barcode_mode(mode)
{
	console.log('set_barcode_mode',mode);
	set_info('');
	barcode_mode = mode;
	button_mode = 'B';
	document.getElementById('button_mode_div').innerHTML = 'B';
	keyboard_str = '';
	// document.getElementsByName('kitchen_manual_barcode')[0].value = '';
	qpack_resume();
}

function get_user(id)
{
	if (chefs) {
		for (var i = 0; i < chefs.length; i++) {
			if (chefs[i]['id'] == id) return (chefs[i]);
		}
	}
	return null;
}

function check_user(user_flag)
{
	console.log('check_user ' + user_flag);
	if (user_flag && user_flag == 1) return true;
	set_info('INVALID USER');
	return false;
}

function process_barcode(s)
{
	let tag = 'process_barcode: ';
	console.log(tag,"process_barcode " + s + " mode " + barcode_mode);
	if (s == 'setup1') {
		console.log(tag,'setup');
	}
	if (user_id <= 0) {
		barcode_mode = 'login';
		if (s.indexOf('acsadmin') >= 0) {
			set_admin();
		}
	}
	if ((s.indexOf('u') >= 0) || (s.indexOf('U') >= 0)) { // user barcode scanned
		
		var uid = parseInt(s.substring(4));
		
		if (barcode_mode == 'login' || user_id <= 0) {
			login(uid);
		}
		
		var user = get_user(uid);
		if (!user) {
			set_info('INVALID USER');
			return;
		}
		set_info('');
		if (barcode_mode == 'dock_QA' && user.supervisor && user.supervisor == 1) {
			dock_QA_scan(uid);
		}
		if (barcode_mode == 'M1' && check_user(user.kitchen)) {
			set_user('m1_chef_id','m_temp_modal4',uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'M1_LR' && check_user(user.kitchen)) {
			set_user('m1_chef_id_LR','m_temp_modal4',uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'force_M1'  && check_user(user.supervisor)) {
			force_M1(uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'force_M3'  && check_user(user.supervisor)) {
			force_M3(uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'KQA_override' && check_user(user.supervisor)) {
			k_qa_override(uid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'PT' && check_user(user.plating)) { // plating team member
			add_team_member(uid);
		}
	}
	if ((s.indexOf('c') >= 0) || (s.indexOf('C') >= 0)) { // component barcode scanned
		var cid = parseInt(s.substring(3));
		if (barcode_mode == 'PT_comp') {
			plating_comp_barcode_scanned(cid);
		}
		else if (barcode_mode == 'plating_batch_change') {
			plating_comp_barcode_scanned(cid,true);
		}
		else if (barcode_mode == 'active_comp') {
			console.log('login for ' + cid);
			for (var i = 0; i < active_comps.length; i++) {
				if (active_comps[i].id == cid) {
					active_comp_selected(i);
					barcode_mode = null;
				}
			}
		}
		else if (barcode_mode == 'kitchen_reprint') {
			for (var i = 0; i < active_comps.length; i++) {
				if (active_comps[i].id == cid) {
					reprint_active_comp_labels(cid);
					barcode_mode = null;
				}
			}
		}
		else if (barcode_mode == 'dock_reprint') {
			reprint_dock_labels(cid);
			barcode_mode = null;
		}
		else if (barcode_mode == 'scan_ingredients') {
			console.log(tag,'read ingredient ' + cid);
			check_ingredient(cid);
		}
		else { // barcode mode null
			console.log(tag,'barcode_mode not set',barcode_mode,mode);
			if (mode == 'plating') {
				// maybe decant?
				plating_comp_barcode_scanned(cid,false);
			}
		}
	}
	document.getElementById('button_mode_div').innerHTML = (barcode_mode)?'B':'-';
}

function set_ingredient_temp(s)
{
	if (s == null) s = last_temp;
	var i = new_comp['read_temp'];
	console.log('set ingredient temp for ',i,s,new_comp['selected_ingredients'][i]['target']);
	
	if (parseInt(s * 10) > parseInt(new_comp['selected_ingredients'][i]['target']) * 10) {
		document.getElementById('m1_temp_div_2a').innerHTML = parseInt(s * 10) / 10 + "&#176C";
		openPage('m_temp_modal1b', this, 'red','m_modal2','tabclass');
		console.log("Too high!!!");
		return;
	}
	new_comp['selected_ingredients'][i]['temp'] = s;
	if (draw_ingredients()) {
		// set_barcode_mode('scan_ingredients');
		// save ingredient - new_comp.php
		// show confirm btn
		document.getElementById('confirm_start_comp_btn').style.display = 'inline-block';
		// start_component(false,true);
	}
	else {
		set_barcode_mode('scan_ingredients');
	}
}

function check_ingredient(cid)
{
	let tag = "check_ingredient: ";
	console.log(tag, cid);
	let error_node=document.getElementById('m1_temp_div_1_error');
	clearChildren(error_node);
	$.ajax({
        url: RESTHOME + "get_active_comps.php?cid=" + cid,
        type: "POST",

        success: function(result) {
        	console.log(tag,"result: ",result);
 
            var scanned_ingredient = JSON.parse(result);
            console.log(tag,"got component " + scanned_ingredient[0].description,' expired:' ,scanned_ingredient[0].expired);
            if (scanned_ingredient[0].expired == 1) {
            	console.log(tag,'ingredient expired');

				error_node = 'EXPIRED';

            	popup_error(scanned_ingredient[0].description,'EXPIRED<br>' + scanned_ingredient[0].expiry_date);
				return;
			}
            var valid_ingredient = false;
            for (var i = 0; i < new_comp['selected_ingredients'].length; i++) {
    			var sub = get_component_by_id(new_comp['selected_ingredients'][i]['id']);
    			if (sub['description'] == scanned_ingredient[0].description) {
    				console.log(tag,"found ingredient");
    				valid_ingredient = true;
    				new_comp['selected_ingredients'][i]['cid'] = scanned_ingredient[0].id;
    				// attach to new_comp and record temperature
    				draw_ingredients();
    				new_comp['read_temp'] = i;
    				read_temp('M0');
    				openPage('m_temp_modal1a', this, 'red','m_modal2','tabclass');
    				var sub = get_component_by_id(new_comp['selected_ingredients'][i]['id']);
    				// d += "<tr><td>" + sub['description'] + '</td>';
    				document.getElementById('ms_1_text').innerHTML = sub['description'];
    				document.getElementById('ms_2_target').innerHTML = ' < ' + get_preptype_val(sub['prep_type'],'M1_temp');
    				new_comp['selected_ingredients'][i]['target'] = get_preptype_val(sub['prep_type'],'M1_temp');
    				console.log(tag,'new_comp: ',new_comp);
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
	let tag = 'save_plating_team: ';
	var data =  {data: JSON.stringify(plating_teams)};
	console.log(tag,"Sent Off: ", data);
	set_barcode_mode (null);
	goto_plating();
	$.ajax({
		url: RESTHOME + "save_plating_teams.php",
		type: "POST",
		data: data,

		success: function(result) {
			console.log(tag,'success: ',result);
			//  goto_m_main();
		},
		fail: (function (result) {
			console.log(tag,'fail: ',result);
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
				// create row
				var tr = document.createElement('tr');
				tr.className = 'plating_tab';
				// create header with team name
				var td = document.createElement('th');
				td.innerHTML = margin('TEAM');
				tr.appendChild(td);
				td = document.createElement('th');
				td.innerHTML = team_id;
				tr.appendChild(td);
				td = document.createElement('th');
				td.innerHTML = ' ';
				tr.appendChild(td);
				tab.appendChild(tr);
			}
			item_count ++;

			// create item entry
			var plating_item = get_plating_item_by_menu_item_id(menu_items[i]['id']);
			if (plating_item) console.log(plating_item);
			var required = parseInt(menu_items[i]['current_shift']) - parseInt(menu_items[i]['current_shift_done']);
		//	if (parseInt(menu_items[i]['current_shift']) > 0) {
			if (required > 0) {
				tr = document.createElement('tr');
				var td = document.createElement('td');
				td.innerHTML = menu_items[i]['code'];

				tr.appendChild(td);
				td = document.createElement('td');

				//figure out color class and link
				var div = "<div onclick='show_menu_item_components(" + menu_items[i]['id'] + ");'>";
				if (plating_item && plating_item.time_started) {  // check plating_item.checked
					div = "<div class='orange' onclick='show_plating_options(" + plating_item.id + ");'>";
					//if (plating_item.time_completed) {
					//	div = "<div class='red'>";
					//}
				}
				else if (parseInt(menu_items[i]['current_shift_done'] > 0)) {
					div = "<div class='red' onclick='show_plating_options(" + plating_item.id + ");'>";
				}
				//finish up
				div += menu_items[i]['dish_name'] + "</div>";

				td.innerHTML = div;
				tr.appendChild(td);
				td = document.createElement('td');
				//var shift = 's' + menu_items[i]['current_shift'];
				//console.log('shift ',shift);
				//console.log(menu_items[i]);
				td.innerHTML = required; //  menu_items[i]['current_shift'];
				tr.appendChild(td);
				tab.appendChild(tr);
			}
		}
	}
}

function goto_plating()
{
	// ???? load_menu_items();
	hide('plating_return');
	hide('plating_print_labels');
	if (active_plating_team == null) {
		goto_plating_teams();
	}
	openPage('m_plating', this, 'red','mobile_main','tabclass');
	openPage('m_plating_sched', document.getElementById('m_plating_team_tab'), 'red','m_modal','tabclass');
	var hd = document.getElementById('active_plating_team_head');
	hd.innerHTML = "Plating Team " + active_plating_team;
	var t = document.getElementById('plating_sched_list');
	clearChildren(t);

	var tab = document.createElement('table');
	tab.className = 'plating_tab';

	var tr = document.createElement('tr');

	var th = document.createElement('th');
	th.innerHTML = 'CODE';
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
		if (pti != active_plating_team) {
			show_plating_items(pti,tab);
		}
	}
	t.appendChild(tab);
}

function plating_batch_change()
{
	console.log('plating_batch_change');
	console.log(plating_item);
	do_show_menu_item_components(plating_item.menu_item_id,true);
	var hd = document.getElementById('active_plating_team_head').innerHTML = "BATCH CHANGE";
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
	var now = new Date();
	var now_ms = now.getTime();
	var event_ms = event_time.getTime(); // time in millisecs
	
	var due_min = get_preptype_val(plating_prep_type,'M2_time_minutes');
	var due_ms = event_ms + due_min * 60 * 1000;  			
	var remaining = (due_ms - now_ms) / (60 * 1000);
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
	

	document.getElementById('chk_plat_temp_item_div').innerHTML = plating_item.code;
	document.getElementById('plating_num_completed').value = 99;
	openPage('plating_temp_div', this, 'red','mobile_main','tabclass');
	openPage('m2_temp_plating', this, 'red','m_modal2','tabclass');
	
}
function record_finish_plating()
{
	console.log("finish_plating");
	var num_completed = document.getElementById('plating_num_completed').value;
	if (parseInt(num_completed) <=0) {
		console.log("invalid number of completed ",num_completed);
		return;
	}
	plating_item.num_completed = num_completed;
	console.log(plating_item);
	
	var data =  {data: JSON.stringify(plating_item)};
    console.log("Sent Off: ", data);
    
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

function decant_labels() 
{
	i = plating_item.active_item;
	var items = plating_item.items;
	var num = document.getElementById('decant_label_num').value;
	console.log('decant_labels ',num);
	if (num < 1) return;
	if (i >= 0 && i < items.length) {
		console.log("selected " + items[i].description);
		console.log(items[i]);
		items[i].decanted_labels = num;
		decant_item_labels(items[i]); 
	}
}

function decant_item_labels(item) 
{
	var data =  {data: JSON.stringify(item)};
	$.ajax({
	    url: RESTHOME + "decant.php",
	    type: "POST",
	    data: data,
	
	    success: function(result) {
	        console.log("decant result ",result);
	        active_comp = JSON.parse(result);
	        print_component_labels(item.decanted_labels);
	        goto_m_main();
	    },
	    
	    fail: (function (result) {
	        console.log("decant fail ",result);
	    })
	});
}

function plating_comp_selected(i,batch_change)
{
	let tag = "plating_comp_selected: ";
	var menu_item = plating_item; // get_menu_item_by_id(active_menu_item_id);
	plating_item.active_item = i;
	plating_item.batch_change = batch_change;
	var items = menu_item.items;
	if (i >= 0 && i < items.length) {
		console.log(tag,"selected ",items[i].description,' item:',items[i]);
	}
	if (items[i].prep_type_id == 2) { // HF decant?
		console.log(tag,'Prep type 2: HF decant?');

		openPage('m_plating_temp_decant', this, 'red','m_modal','tabclass');
		document.getElementById('chk_plating_item_temp_divA').innerHTML = items[i].description;
	} else {
		console.log(tag,'normal prep types not HF');
		// called here because it clears temperature fields and stuff
		read_plating_M1temp();
		let icon_anchor = document.getElementById('m_plating_temp').children[0].getElementsByClassName('temp_icon_anchor')[0];
		console.log(tag,'icon_anchor: ',icon_anchor);
		checkTempDiv(
			icon_anchor,
			plating_item,
			"read_plating_M1temp()");
		openPage('m_plating_temp', this, 'red','m_modal','tabclass');
	}
	
	document.getElementById('chk_plating_item_temp_div').innerHTML = items[i].description;
	document.getElementById('chk_plating_item_exp_div').innerHTML = items[i].expiry_date;
}

function plating_comp_barcode_scanned(cid,batch_change)
{
	
	console.log('plating_comp_barcode_scanned',cid);

	$.ajax({
        url: RESTHOME + "get_active_comps.php?cid=" + cid,
        type: "POST",

        success: function(result) {
        	console.log(result);
        	
        	var comps = JSON.parse(result);
            if (comps && comps.length > 0) {
            	var comp = comps[0];
            	process_scanned_plating_comp(comp,batch_change);
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

}

function process_scanned_plating_comp(comp,batch_change)
{
	let tag = 'process_scanned_plating_comp: ';
	// console.log('plating_comp_barcode_scanned '  + barcode_id);
	// find item in active components

	console.log(tag,"plating_comp_barcode_scanned found " + comp.description + ' ' + comp.expired);
	if (comp.expired == 1) {
		console.log(tag,'item expired');
		//fail anchor node
		let anchor = document.getElementById('m2_pt_sl_div');
		clearChildren(anchor);

		anchor.appendChild(icon_fail());
		anchor.appendChild(new_node('div',"ITEM EXPIRED","red center"));
		anchor.appendChild(new_node('div',comp.description,"red center"));
		anchor.appendChild(new_node('div','expired ' + comp.expiry_date,"red center"));

		openPage('plating_temp_div', this, 'red','mobile_main','tabclass');
		openPage('m2_sl_plating', this, 'red','m_modal2','tabclass');
		
		return; // jump to expired page
	}
	if (plating_item && plating_item.items) {
		let items = plating_item.items;
		console.log("now checking plating item " + items.length,batch_change);
		for (var i = 0; i < items.length; i++) {
			// clear id if M1_temp not set
			if (!items[i].M1_temp) {
				console.log('clearing id ',items[i].component_id);
				items[i].component_id = null;
			}
			if (items[i].description == comp.description && items[i].component_id == comp.id && (!items[i].M1_temp || batch_change)) {
				console.log('component already entered',items[i].component_id, comp.id);
				return;
			}
		}
		for (var i = 0; i < items.length; i++) {
			if (items[i].description == comp.description && items[i].component_id != comp.id && (!items[i].M1_temp || batch_change)) {
				console.log('found component',items[i].component_id, comp.id);
				items[i].M1_temp = null; // reset for batch_change
				items[i].checked = true;
				items[i].component_id = comp.id;
				items[i].expiry_date = comp.expiry_date;
				console.log("found item",i,batch_change);
				plating_comp_selected(i,batch_change);
				return;
			}		
		}
	}
	// didn't find it - maybe decant?
	if (comp.prep_type == 'HF') {
		console.log(plating_items);
		console.log('-----------');
		// find item
		for (var i = 0; i < plating_items.length; i++) {
			
			for (var j = 0; j < plating_items[i].items.length; j++) {
				if (plating_items[i].items[j].component_id == comp.id) {
					console.log("found item ");
					console.log(plating_items[i].items[j]);
					plating_item = plating_items[i];
					plating_item.active_item = j;
					openPage('m_plating', this, 'red','mobile_main','tabclass');
					openPage('m_plating_temp_decant', this, 'red','m_modal','tabclass');
					document.getElementById('chk_plating_item_temp_divA').innerHTML = comp.description;
				}
			}
			
		}
		
		
	}
}
function goto_active_plating()
{
	show_menu_item_components(active_menu_item_id)
}

function batch_change_component(comp)
{
	var data =  {data: JSON.stringify(comp)};
    console.log("batch_change_component Sent Off: ", data);
    
    $.ajax({
        url: RESTHOME + "batch_change.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log("batch_change_component ",result);
            goto_plating();
        },
        
        fail: (function (result) {
            console.log("batch_change_component fail ",result);
        })
    });
}

function set_plating_M1_temp(temperature) 
{
	let tag = "set_plating_M1_temp: ";
	console.log(tag, plating_item.description, " -> ",
			plating_item.items[plating_item.active_item].description);

	let tempdiv = document.getElementById('chk_plating_item_temp');

	// check temp is below M1_temp
	if (parseInt(temperature) < PLATING_M1_TEMP) {
		console.log(tag, 'temp low enough');
		plating_item.items[plating_item.active_item].M1_temp = temperature;
		plating_item.items[plating_item.active_item].M1_time = null;
		if (plating_item.batch_change) {
			console.log(tag,'batch change temp',temperature);
			plating_item.items[plating_item.active_item].replace = plating_item.items[plating_item.active_item].id;
			plating_item.items[plating_item.active_item].plating_team_id = plating_item.team_id;
			console.log(plating_item);
			console.log(plating_item.items[plating_item.active_item]);
			batch_change_component(plating_item.items[plating_item.active_item]);
			// record new component and got back to main plaing screeen
		} else {
			goto_active_plating();
		}
	} else {
		//set red
		show_temp(temperature,true);
		//set recheck text
		let icon_anchor = document.getElementById('m_plating_temp').children[0].getElementsByClassName('temp_icon_anchor')[0];
		console.log(tag,'icon_anchor: ',icon_anchor);
		checkTempDiv(
			icon_anchor,
			plating_item,
			"read_plating_M1temp()",
			true);
		console.log(tag, 'temp too high');
	}
}

function set_plating_M2_temp(temperature) 
{
	console.log("set_plating_M2_temp " );
	console.log(plating_item);
	plating_item.M2_temp = temperature;
	var temp_target = get_preptype_val(plating_prep_type,'M2_temp');
	if (parseInt(temperature) <= temp_target) {
		// console.log('record_finish_plating()');
		// record_finish_plating();
	}
	else {
		console.log("plating m2 too high");
	}
	// record how many meal items were plated
	document.getElementById('chk_plating_item_qty_div').innerHTML = plating_item.code;
	document.getElementById('plating_num_completed').value = plating_item.num_labels;
	console.log('open page m_plating_finished');
	openPage('m_plating_finished', this, 'red','m_modal2','tabclass');


	
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
    console.log("Sent Off: ", data);
    
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
    console.log("Sent Off: ", data);
    console.log(plating_item);
    
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
            plating_item['expiry_date'] = p['expiry_date'];
            
            
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

function do_show_menu_item_components(menu_item_id,batch_change)
{
	let tag = 'do_show_menu_item_components: ';
	set_barcode_mode("PT_comp");
	openPage('m_plating_sched', this, 'red','m_modal','tabclass');
	active_menu_item_id = menu_item_id; // global - so we can come back to it
	// var div = document.getElementById('menu_item_components_div');
	plating_item = find_plating_item(menu_item_id);
	console.log(tag,"plating_item: ",plating_item);
	if (plating_item == null) { // 
		console.log(tag, "ERROR plating item is null");
		// plating_item = Object.create(get_menu_item_by_id(menu_item_id)); // possibly dangerous .....
	}
	var div = document.getElementById('plating_sched_list');
	clearChildren(div);
	var tab = document.createElement('table');
	tab.className = 'item_table';
	var tr = document.createElement('tr');
	var th = document.createElement('th');
	th.width = '70%';
	th.innerHTML= margin(plating_item.dish_name + "<br>" + plating_item.code + "<BR>Required: " + plating_item.current_shift);
	document.getElementById('pt_description_labels').innerHTML = plating_item.current_shift;
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML=margin('S/L');
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML=margin('TEMP');
	tr.appendChild(th);
	tab.appendChild(tr);

	var all_good = true; // check before useby date and temp measured ok
	if (plating_item != null) {
		console.log(tag,"found menu_item ",plating_item.dish_name,plating_item.items.length);
		console.log(tag, 'plating_item: ', plating_item, JSON.stringify(plating_item));
		var items = plating_item.items;	
		for (let i = 0; i < items.length; i++) {
			if (!items[i].M1_temp) {
				console.log('clearing id ',items[i].component_id);
				items[i].component_id = null;
			}
			console.log("found ",items[i].description,items[i].time_completed);
			if (items[i].time_completed && items[i].time_completed.length > 2) {
				// item already used - what to do?
			}
			else {
				tr = document.createElement('tr');

				//this does not actually set the right component, resulting in missing shelf life and failure to complete plating
				//var clickdiv = "<div onclick='plating_comp_selected(" + i + ");'>" + items[i].description + "</div>";
				var clickdiv = "<div>" + items[i].description + "</div>";

				// show items in coolroom ready to be plated
			 	// clickdiv += show_plating_comps(items[i].description);
				// tr.appendChild(new_td(items[i].description,'item'));

				tr.appendChild(new_td(clickdiv,'item'));
				var td = document.createElement('td');
				td.id = 'plating_item_checked_' + i;

				if (items[i].component_id) {
					let d = new Date;
					let dex = new Date(items[i].expiry_date);
					if (dex.getTime() > d.getTime()) {

						// TODO actually check SL here
						td.appendChild(icon_pass());
					} else {
						console.log(tag, 'item ',i, ' expires', items[i].expiry_date, dex);
						console.log(tag, 'item ',i, ' not expired: ', dex.getTime() > d.getTime(), dex.getTime(), '>', d.getTime());

						td.appendChild(icon_fail());

						console.log(tag, 'item[',i,'] is expired');
						all_good = false;
					}
				} else {
					td.innerHTML = '-';
					console.log(tag, 'item[',i,'].component_id is empty');
					all_good = false;
				}
				tr.appendChild(td);
				td = document.createElement('td');
				td.id = 'plating_item_temp_' + i;
				td.innerHTML = '-';
				if (items[i].M1_temp) {
					td.innerHTML = items[i].M1_temp;
				} else {
					all_good = false;
				}
				tr.appendChild(td);
				tab.appendChild(tr);
			}
		}
		div.appendChild(tab);
		
		if (all_good) {
			if (batch_change) {
				hide('plating_return');
				hide('plating_print_labels');
				show('plating_batch_change_btns');
				set_barcode_mode('plating_batch_change');
			}
			else {
				hide('plating_return');
				hide('plating_batch_change_btns');
				show('plating_print_labels');
				plating_item.checked = true;
			}
		}
		else {
			show('plating_return');
			hide('plating_batch_change_btns');
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
	// hide('kitchen_manual_code');
}

function new_component() {
	console.log('adding ' + $('#search').val());
	var component = new Object();
	component.description = $('#search').val();
	
	// component.prep_type = new_comp['prep_type'];
	
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: ", data);
    console.log(component);
    $.ajax({
        url: RESTHOME + "new_component.php",
        type: "POST",
        data: data,

        success: function(result) { 
        	load_comps(component_selected);
        },
        fail: (function (result) {
            console.log("new _component fail ",result);
        })
    });
}

function show_dock_component(cid)
{
	// show('dock_display_comp_div');
	//
	// var div = document.getElementById('dock_display_comp_div1');
	// div.innerHTML = '';

	// TODO check if necessary
	new_comp = get_component_by_id(cid);
	if (!new_comp) {
		alert("ERROR");
		return;
	}
	console.log('show_dock_component: ',new_comp);

	// TODO take some to M1?
	// var flds = ['description','supplier','product','spec','shelf_life_days'];
	// for (var i =0; i < flds.length; i++) {
	// 	var d = document.createElement('div');
	// 	d.className = 'smaller';
	// 	d.innerHTML = flds[i] + ":";
	// 	div.appendChild(d);
	// 	var d = document.createElement('div');
	// 	d.className = 'small';
	// 	if (new_comp[flds[i]] == null) {
	// 		d.innerHTML = "NOT SET";
	// 	}
	// 	else {
	// 		d.innerHTML = new_comp[flds[i]];
	// 	}
	//
	// 	div.appendChild(d);
	// }

	// show preptype details
	// var ptid = new_comp['prep_type'];
	//
	// var d = document.createElement('div');
	// d.className = 'smaller';
	// d.innerHTML = "Prep Type:";
	// div.appendChild(d);
	// var d = document.createElement('div');
	// d.className = 'small';
	// d.innerHTML = get_preptype_val(ptid,'code');
	//
	// div.appendChild(d);
	// show('dock_comp_selected_btns');

	dock_read_M1temp()
}

function show_dock()
{
	openPage('dock_main', null, 'red','mobile_main','tabclass');
	openPage('m_dock',document.getElementById('supplier_list_tab'), 'red','m_modal','tabclass');
	show('dock_search_div');
	$('#dock_search').val('');
	var dock_items = new Array();
	var div = document.getElementById('dock_display_comp_div1');
	div.innerHTML = '';
	var table = document.createElement('table');
	table.width = '100%';
	for (var i = 0; i < comps.length;i++) {
		if (comps[i].high_risk == 1) {
			var tr = document.createElement('tr');

			var func = '<div class="m-5" onclick="show_dock_component('+comps[i]['id']+');" >';

			var td = document.createElement('td');
			td.innerHTML = func+comps[i]['description']+'</div>';
			tr.appendChild(td);

			var td = document.createElement('td');
			td.innerHTML = func+comps[i]['supplier']+'</div>';

			comps[i].label = comps[i]['supplier'] + ": " + comps[i]['description'];
			
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
		response: function (event, ui) {
			// console.log("search response found " + ui.content.length); console.log(ui);
			/*
            if (ui.content.length == 0) {
                document.getElementById('new_comp_btns').style.display = 'block';
            }
            else {
                document.getElementById('new_comp_btns').style.display = 'none';
            } */
		},
		select: function (event, ui) {
			// place the person.given_name value into the textfield called 'select_origin'...
			$('#dock_search').val(ui.item.label);
			// and place the person.id into the hidden textfield called 'link_origin_id'.
			console.log('setup_dock_search: selected ', ui.item.value);
			show_dock_component(ui.item.value);
			// cordova.plugins.Keyboard.close();
			return false;
		}
	})
}

function goto_dock()
{
	load_comps(show_dock);
	mode = 'dock';
}

function goto_m_main(new_mode)
{
	if (new_mode) mode = new_mode;
	load_preptypes(); // try to keep up to date. should be smarter
	$('#search').val('');
	$('#search').focus(function(){load_comps();});
	switch (mode) {
		case 'kitchen':
			openPage('mm2', document.getElementById('m_current_tracking_tab'), 'red','mobile_main','tabclass');
			m_tracking();
			break;
		case 'dock':
			openPage('dock_main', document.getElementById('m_plating_team_tab'), 'red','mobile_main','tabclass');
			break;
		default:
			goto_plating();
			break;
	}
}

function get_preptype_val(id,fld)
{
	for (let i = 0; i < preptypes.length; i++) {
		if (preptypes[i].id == id) {
			return(preptypes[i][fld]);
		}
	}
	return("not found");
}


function draw_ingredients() // returns true if all ingredients are selected and have a temperature
{
	let tag = 'draw_ingredients: ';
	let finished = true;
	document.getElementById('confirm_start_comp_btn').style.display = 'none';
	openPage('m_temp_modal1', this, 'red','m_modal2','tabclass');

	clearChildren(document.getElementById('m1_temp_div_1a'));
	// clearChildren(document.getElementById('chk_temp_item_id_div'));

	document.getElementById('chk_temp_item_div').innerHTML = new_comp.description;

	let div = document.getElementById('m1_temp_div_1');
	var d = "<div class='m-10'><table id='comp_ingredients_table'>";

	d += "<tr><td width='200px'>Description</td><td width='40px'>ID</td><td width='40px'>Temp</td></tr>";
	var prep_type_id = new_comp['prep_type'];
	console.log(tag,'prep_type_id',prep_type_id);

	for (var i = 0; i < new_comp['selected_ingredients'].length; i++) {
		var sub = get_component_by_id(new_comp['selected_ingredients'][i]['id']);
		d += "<tr><td>" + sub['description'] + '</td>';

		if (new_comp['selected_ingredients'][i]['cid']) {
			d += "<td>" + new_comp['selected_ingredients'][i]['cid'] + "</td>";
		} else {
			finished = false;
			d += "<td>-</td>";
		}

		if (new_comp['selected_ingredients'][i]['temp']) {
			d += "<td>" + new_comp['selected_ingredients'][i]['temp'] + "</td>";
		} else {
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
	let tag = "component_selected: ";

	console.log(tag,"loading chefs");
	load_chefs(null);

	new_comp = null;
	active_comp = null;

	if (id) {
		new_comp = get_component_by_id(id);
	}
	console.log(tag, 'new_comp: ', new_comp);
	if (!new_comp) {
		console.log(tag,"can't find component - search for " + $('#search').val());
		new_comp = get_component_by_description($('#search').val());
	}
	if (new_comp['prep_type'] < 1) new_comp['prep_type'] = 1;
	console.log(tag, 'updated new_comp', new_comp);

	let prep_type_id = new_comp['prep_type'];
	console.log(tag,'prep_type_id: ',prep_type_id);

	if (prep_type_id < 1) prep_type_id = 1;
	
	let M1_temp = get_preptype_val(prep_type_id,'M1_temp');
	let prep_type_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	new_comp.shelf_life_days = get_preptype_val(prep_type_id,'shelf_life_days');

	let sign = (prep_type_sign == 0)?' < ':' > ';

	openPage('m_temp', this, 'red','mobile_main','tabclass');

	console.log(tag,"new_comp: ",new_comp," subcomponents: ", new_comp['subcomponents']);
	// subcomponents is an array of ids - needs to become an array of objects to store temperature and used id

	clear_comp_fields();

	// document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];

	if (new_comp['subcomponents']) {

		if (!new_comp['selected_ingredients']) {
			new_comp['selected_ingredients'] = new Array();
			for (let i = 0; i < new_comp['subcomponents'].length; i++) {
				new_comp['selected_ingredients'][i] = new Object();
				new_comp['selected_ingredients'][i]['id'] = new_comp['subcomponents'][i];
			}
		}
		console.log(tag,'has ingredients');
		set_barcode_mode('scan_ingredients');
		draw_ingredients();
	} else if (M1_temp == null) {
		// low risk. No temp required
		console.log(tag,"LOW RISK");
		set_barcode_mode("M1_LR");
		openPage('m_temp_modal_LR', this, 'red','m_modal2','tabclass');
		document.getElementById('m1_temp_div_LR_comp').innerHTML = new_comp['description'];

		clearChildren(document.getElementById('chk_temp_item_div'));
		clearChildren(document.getElementById('chk_temp_item_id_div'));
	} else {
		set_barcode_mode("M1");
		set_temp_mode("M1");
		openPage('m_temp_modal', this, 'red','m_modal2','tabclass');
		document.getElementById('ms_2').innerHTML = 'M1';
		document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
		document.getElementById('ms_2_target').innerHTML = sign + get_preptype_val(prep_type_id,'M1_temp') + "&#176";
		document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];
		// document.getElementById('chk_temp_item_id_div').innerHTML = sprintf('C01%06d',new_comp['id']);

		clearChildren(document.getElementById('chk_temp_item_id_div'));

		temp_probe = false;

		let temp_div = document.getElementById('m1_temp_div');
		checkTempDiv(temp_div,new_comp,"read_M1temp();");
	}

	document.getElementById('chk_temp_pt_div').innerHTML = get_preptype_val(prep_type_id,'code');
}

function checkTempDiv(anchor,comp,onclick,recheck = false) {
	clearChildren(anchor);

	appendSensorImage(anchor,comp,onclick);

	let div = document.createElement('div');
	let text;
	if(recheck) {
		text = "<b> RECHECK POSSIBLE </b></br>";
	}else {
		text = "<b> CHECK THE TEMPERATURE </b></br>";

		text += "USE ";
		console.log("checkTempDiv: component probe type is:"+comp['probe_type']);
		if (comp['probe_type'] && comp['probe_type'] == 2) {
			text += "PROBE";
		} else {
			text += "IR SENSOR";
		}
	}
	div.innerHTML = text;
	div.className = "center";
	div.id = "temp_instruction";
	anchor.appendChild(div);
}

function appendSensorImage(anchor,comp,onclick){
	let sensor;
	if (comp['probe_type'] && comp['probe_type'] == 2) {
		sensor=iconProbe();
	} else {
		sensor=iconIR();
	}
	sensor.setAttribute("onclick",onclick);
	anchor.appendChild(sensor);
}

function show_product_details(comp) 
{
	// rewrite using array of objects - or table builder......
	var flds = ['description','product','shelf_life_days','spec','supplier'];
	var hds = ['Description','Product','Shelf life','Spec','Supplier'];
	var tab = document.createElement('table');
	tab.style.position = 'absolute';
	tab.style.left = '30px';
	for (var i = 0; i < flds.length; i++) {
		var tr = document.createElement('tr');
		tr.appendChild(new_td(hds[i],'comp','m-5'));
		tr.appendChild(new_td(comp[flds[i]],'comp','m-5'));
		tab.appendChild(tr);
	}
	return(tab);
	
}

function dock_read_M1temp(callback)
{
	console.log('dock_read_M1temp');
	console.log(active_comp);
	load_chefs(null);
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	
	//document.getElementById('dock_m1_temp_div').innerHTML = new_comp['description'];
	document.getElementById('dock_m1_temp_div').innerHTML = null;
	document.getElementById('dock_m1_temp_div').appendChild(show_product_details(new_comp));
	show('dock_m_temp_modal');
	read_temp('M1_dock');
}

function read_M1temp(callback){
	load_chefs(null);
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	// clearChildren(document.getElementById('m1_temp_div'));
	read_temp('M1');
}

function read_M2temp(callback){
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	clearChildren(document.getElementById('m1_temp_div'));
	read_temp('M2');
}

function read_pt_M2temp(callback){
	// document.getElementById('m1_temp_div').innerHTML = 'checking temperature';
	clearChildren(document.getElementById('m1_temp_div'));
	read_temp('M2_plating');
}

function read_plating_M1temp(callback){
	
	// document.getElementById('m1_temp_div').innerHTML = '';
	clearChildren(document.getElementById('chk_plating_item_temp'));
	read_temp('M1_plating');
}

function check_temp_m1_dock(t)
{
	console.log("check temp dock",t);
	new_comp.M1_temp = t; // 
	active_comp = new_comp;
	
	console.log(new_comp);
	
	// var t = document.getElementsByName('m1_temp')[0].value;
	var prep_type_id = new_comp['prep_type']; // should always be 6,7 or 8 (DOCK)
	var M1_temp_target = get_preptype_val(prep_type_id,'M1_temp');
	var M1_temp_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	// document.getElementById('dock_m1_temp_div').innerHTML = new_comp['description'];
	document.getElementById('dock_m1_temp_div').innerHTML = '';
	document.getElementById('dock_m1_temp_div').appendChild(show_product_details(new_comp));
	show_product_details(new_comp) 
	
// 	document.getElementById('dock_m1_temp_div_2').innerHTML=parseInt(t) + "&#176C"
	//document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C"
/*	document.getElementById('dock_m1_temp_div_4').innerHTML= parseInt(t * 10) / 10 + "&#176C";
	document.getElementById('dock_m1_temp_div_5').innerHTML= new_comp['description'];
	document.getElementById('dock_m1_temp_div_6').innerHTML= parseInt(t * 10) / 10 + "&#176C"; */
	console.log("check temp",t,M1_temp_target);
	if (t.length > 0) {
		if (M1_temp_sign == 1) {// should never happen
			alert("incorrect prep type");
		}
		else { 
			if (parseInt(t * 10 ) > parseInt(M1_temp_target * 10)) { // round to one decimal place
				show_temp(t,true);
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
	let tag = "check_temp: ";
	console.log(tag, "t: ", t);
	new_comp.M1_temp = t;
	
	console.log(tag, "new_comp: ", new_comp);
	
	// var t = document.getElementsByName('m1_temp')[0].value;
	var prep_type_id = new_comp['prep_type'];
	var M1_temp_target = get_preptype_val(prep_type_id,'M1_temp');
	var M1_temp_sign = get_preptype_val(prep_type_id,'M1_temp_above');
	//

	let m1_temp_div_2 = document.getElementById('m1_temp_div_2');
	m1_temp_div_2.innerHTML=parseInt(t) + "&#176C";
	document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C";
	document.getElementById('m1_temp_div_4').innerHTML=parseInt(t) + "&#176C";

	let m1_temp_div_2a = document.getElementById('m1_temp_div_2a');
	let m1_temp_div_2icon = document.getElementById('m1_temp_div_2icon');

	console.log(tag,"t: ",t," target: ",M1_temp_target);



	if (t.length > 0) {
		if (M1_temp_sign == 1) {
			if (parseFloat(t) < parseFloat(M1_temp_target)) {
				console.log(tag,"M1 temp too low: ",parseFloat(t)," < ",parseFloat(M1_temp_target));
				show_temp(t,true);
				openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
				checkTempDiv(
					m1_temp_div_2icon,
					new_comp,
					"openPage('m_temp_modal', this, 'red','m_modal2','tabclass')",
					true
				);
			} else {
				set_barcode_mode("M1");
				openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			}
		} else {
			if (parseFloat(t) > parseFloat(M1_temp_target)) {
				console.log(tag,"M1 temp too high: ", parseFloat(t)," > ", parseFloat(M1_temp_target));
				show_temp(t,true);
				openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
				checkTempDiv(
					m1_temp_div_2icon,
					new_comp,
					"openPage('m_temp_modal', this, 'red','m_modal2','tabclass')",
					true
				);
			} else {
				set_barcode_mode("M1");
				openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			}
		}
	}
	
}

function add_chef_select(target_div,input_name) 
{
	let s = document.getElementById(target_div);
	clearChildren(s);

	let select = document.createElement('select');
	select.name = input_name;
	// console.log('found plating teams ',plating_teams.length);
	for (var i = 0; i < chefs.length; i++) {
		let option = document.createElement( 'option' );
		option.value = chefs[i]['id'];
		option.textContent =  chefs[i]['label'];
		select.appendChild( option );
		// console.log(i);
	}
	s.appendChild(select);
}

function dock_start_component()
{
	console.log('dock_start_component');
	console.log(new_comp);
	console.log(active_comp);

	active_comp.dock = true;
	active_comp.comp_id = new_comp['id'];
	
	active_comp.finished = 'true';
	active_comp.M1_chef_id = get_user_id();

	let data = {data: JSON.stringify(active_comp)};

	console.log("dock_start_component Sent Off: ");
	console.log(active_comp);
	let qty_input = 'dock_m1_label_qty';

	$.ajax({
		url: RESTHOME + "new_comp.php",
		type: "POST",
		data: data,

		success: function (result) { // need to get the id of the new component back to print labels
			console.log("dock_start_component success ", result);
			var comp = JSON.parse(result);

			console.log("start_component id = ", comp.id);
			var qty = document.getElementsByName(qty_input)[0].value;

			active_comp.id = comp.id;
			active_comp.expiry_date = comp.expiry_date;
			active_comp.M1_time = comp.M1_time;

			// active_comp.M1_chef_id = comp.M1_chef_id;
			print_component_labels(qty);

			// reset default value TODO should that not be done on popup?
			document.getElementsByName(qty_input)[0].value = 1;
			goto_dock();

		},
		fail: function (result) {
			console.log("dock_start_component fail ", result);
		}
	});
}

function setup_force_M1() //
{
	document.getElementById('m2_temp_div_2b').innerHTML = 'SCAN AUTHORISED ID';
	set_barcode_mode('force_M1');
}

function force_M1(uid)
{
	console.log('force_M1');
	var i = new_comp['read_temp'];
	new_comp['selected_ingredients'][i]['temp'] = last_temp;
	new_comp.force_M1_uid = uid;
	if (draw_ingredients()) {
		// set_barcode_mode('scan_ingredients');
		// save ingredient - new_comp.php
		start_component(false,true);
	} else {
		set_barcode_mode('scan_ingredients');
	}
	console.log(active_comp);
	new_comp.force_M1_uid = uid;
	new_comp.force_M1_temp = last_temp;
}

function setup_force_M3() //
{
	document.getElementById('m2_temp_div_2a').innerHTML = '<img src="img/icon_barscan.svg"\n' +
		'                     class="icon_barscan"><span class="scan_instruction"> SCAN AUTHORISED ID </span>';
	set_barcode_mode('force_M3');
}

function force_M3(uid)
{
	console.log('force_M3');
	console.log(active_comp);
	active_comp.force_M3_uid = uid;
	active_comp.force_M3_temp = last_temp;
	console.log(active_comp);
	var chef = get_chef_by_id(uid);
	document.getElementById('force_M3_signoff_uid').innerHTML = chef['label'];
	openPage('m2_temp_modal_force_M3', null, 'red','m_modal2','tabclass');

}

function k_qa_override(uid)
{
	let tag = 'k_qa_override: ';
	active_comp.qa_override_uid = uid;
	active_comp.qa_override_temp = last_temp;
	console.log(tag,'active_comp: ',active_comp);
	var chef = get_chef_by_id(uid);
	if (active_comp['M2_time'] && active_comp['M2_time'].length > 1) { // at M3
		document.getElementById('force_M3_overdue_uid').innerHTML = chef['label'];
		show_temp(last_temp);
		openPage('m2_temp_overdue_B', null, 'red','m_modal2','tabclass');
	}
	else {  // at M2
		document.getElementById('m2_temp_overdue_div_2').innerHTML = last_temp;
		document.getElementById('force_M2_overdue_uid').innerHTML = chef['label'];
		show_temp(last_temp);
		openPage('m2_temp_overdue_M2B', null, 'red','m_modal2','tabclass');
	}
}

function clear_temps()
{
    tdiv = document.getElementsByClassName('temp_reading');
    console.log("clear_temps: Clearing "+tdiv.length+" temperature divs");
    for (i = 0; i < tdiv.length; i++) {
    	//console.log("found div ",tdiv[i].id);
    	try {
    		clearChildren(tdiv[i]);
    	}
	catch (e) {
        	console.log("who knows.....");
        }
    }
}
function show_temp(t,overtemp = false)
{
	console.log('show_temp',t,overtemp);
    tdiv = document.getElementsByClassName('temp_reading');
    for (i = 0; i < tdiv.length; i++) {
    // 	console.log("found div ",tdiv[i].id);
    //	try {
    		tdiv[i].innerHTML= parseInt(t * 10) / 10 + "&#176C";
    		if (overtemp == true) tdiv[i].style.color = 'var(--acs_red)';
    		else tdiv[i].style.color = 'var(--acs_green)';
   // 	}
   //     catch (e) {
   //     	console.log("show_temp who knows.....");
  //      }
    }
}

function discard_component_popup()
{
	console.log('discard component popup');
	console.log(active_comp);
	document.getElementById('popup_discard_msg').innerHTML = '<h2>DISCARD</h2>' + active_comp.description;
	show("popup_discard_div");
}
function discard_component()
{
	console.log('discard component');
	console.log(active_comp);
	close_popup("popup_discard_div");
	var data =  {data: JSON.stringify(active_comp)};
	console.log("delete component Sent Off: ", data);
	$.ajax({
		url: RESTHOME + "delete_comp.php",
		type: "POST",
		data: data,

		success: function(result) { // need to get the id of the new component back to print labels
			console.log("discard_component result ",result);
			goto_m_main();
		},
		fail: (function (result) {
			console.log("discard_component fail ",result);
		})
	});
}

close_popup("popup_discard_div");

function start_component(dock,at_M0)
{
	let tag = 'start_component: ';
	// object copy is messy - TODO
	load_chefs(add_chef_select('m1_temp_div_chef','m1_chef_id'));

	console.log(tag,'active component', active_comp,'new component', new_comp);

	// check if component at M0 - has ingredients
	if ( active_comp && ( !active_comp['M1_time'] || active_comp['selected_ingredients'] )) {
		console.log(tag,'need to print labels');
		active_comp.label_qty = document.getElementsByName('m1_label_qty')[0].value;
		comp_milestone(active_comp['M1_temp']);
		goto_m_main();
		return;
	}

	// let component = new Object();

	//component.description = new_comp['description']; // simplifies display
	new_comp.comp_id = new_comp['id'];
//	component.M1_chef_id = new_comp['M1_chef_id'];
//	component.prep_type = new_comp['prep_type'];
//	component.M1_action_code = new_comp['M1_action_code'];
//	component.M1_action_id = new_comp['M1_action_id'];
//	component.shelf_life_days = new_comp.shelf_life_days;
	new_comp.items = new_comp.selected_ingredients;
	// component.M1_temp = document.getElementsByName('m1_temp')[0].value;
	let M2_time = get_preptype_val(new_comp.prep_type,'M2_time_minutes');
	console.log(tag, "At M1, M2 time = " + M2_time
		+ ", M3 time = " + get_preptype_val(new_comp.prep_type,'M3_time_minutes'));
	
	if (M2_time == null) {
		new_comp.finished = 'true';
	} else {
		new_comp.M1_temp = new_comp['M1_temp'];
		console.log(tag, 'setting M1_temp to ', new_comp.M1_temp);
	}
	
	active_comp = new_comp;
	let data = {data: JSON.stringify(active_comp)};

	console.log(tag, 'sent off:', data);
	let qty_input = (dock) ? 'dock_m1_label_qty' : 'm1_label_qty';

	$.ajax({
		url: RESTHOME + "new_comp.php",
		type: "POST",
		data: data,

		success: function (result) {
			// need to get the id of the new component back to print labels
			console.log(tag,"success ", result);
			let comp = JSON.parse(result);

			console.log(tag,"returned id ", comp.id);
			let qty = document.getElementsByName(qty_input)[0].value;

			active_comp.id = comp.id;
			active_comp.expiry_date = comp.expiry_date;
			active_comp.M1_time = comp.M1_time;

			// active_comp.M1_chef_id = comp.M1_chef_id;
			if (!at_M0) {
				console.log(tag,"not at M0, printing ",qty, " labels");
				print_component_labels(qty);
			}

			// reset default value TODO should that not be done on popup?
			document.getElementsByName(qty_input)[0].value = 1;
			goto_m_main();
			new_comp = null;
		},
		fail: function (result) {
			console.log(tag, "fail ", result);
		}
	});
}

function set_user(input_name,next_page,uid) {
	let tag = 'set_user: ';

	if (uid == 0) {
		uid = document.getElementsByName(input_name)[0].value;
	} else {
		document.getElementsByName(input_name)[0].value = uid;
	}
	console.log(tag,"got user id ",uid);

	let chef = get_chef_by_id(uid);
	if (new_comp == null && active_comp != null){
		new_comp = active_comp;
	}
	if (chef) {
	    console.log(tag,"found chef ",chef['label']);
	    console.log(tag,"new_comp", new_comp);
	    document.getElementById('m1_temp_div_5').innerHTML = chef['label'];
	    if (new_comp['M1_temp']){
			show('m_temp_modal4a');
		} else {
	    	hide('m_temp_modal4a');
		}
	    openPage(next_page, this, 'red','m_modal2','tabclass');
	    new_comp['M1_chef_id'] = uid;
	    if (active_comp) {
	    	active_comp['M1_chef_id'] = uid;
		}
		console.log(tag,"new_comp", new_comp);
	}
}

function comp_milestone(temp_reading,force,qa_code) {
	let tag = 'comp_milestone: ';
	// send data to REST interface
	console.log(tag, 'active_comp: ',active_comp);
	let prep_type_id = active_comp['prep_type_id'];

	document.getElementById('dock_m1_temp_div_4').innerHTML=parseInt(temp_reading * 10) / 10 + "&#176C"
	let temp_target = get_preptype_val(prep_type_id,'M2_temp');
		
	let component = new Object();
	component.id = active_comp['id'];
	let url = '';
	if (force && qa_code) {
		console.log(tag,'forced by QA');
		component.M3_temp = temp_reading;
		component['M2_time'] = 'now';
		component['M3_time'] = 'now';
		component['M3_action_code'] = qa_code;
		component['M3_action_id'] = active_comp['force_M3_uid'] ;
		component['M3_temp'] = last_temp ;
		component.M3_chef_id = 0;
		url = RESTHOME + 'M3_comp.php';
	} else if (active_comp['M1_time'] == '') {
		// M1
		console.log(tag,'M1: component:', component, 'active_comp:',active_comp);

		// component.M2_temp = document.getElementsByName('m2_temp')[0].value;
		component.M1_temp = last_temp;
		component.M1_chef_id = active_comp['M1_chef_id']; //

		url = RESTHOME + 'M1_comp.php';
	} else if (active_comp['M2_time'] == '') {
		// M2
		console.log(tag,'M2');
		// component.M2_temp = document.getElementsByName('m2_temp')[0].value;
		component.M2_temp = last_temp;
		active_comp['M2_time'] = 'now';
		component.M2_chef_id = 0;
		if (qa_code) {
			console.log(tag,'setting action code');
			component['M2_action_code'] = qa_code;
			component['M2_action_id'] = active_comp.qa_override_uid ;
		}
		var M3_time_minutes = get_preptype_val(prep_type_id,'M3_time_minutes');
		console.log(tag,"At M2, M3 time = " + M3_time_minutes + " ->" + typeof(M3_time_minutes));
		if (M3_time_minutes == null) {
			console.log(tag,"component finished");
			component.finished = 'true';
		}
		url = RESTHOME + 'M2_comp.php';
	} else {
		console.log(tag,'M3');
		//component.M3_temp = document.getElementsByName('m2_temp')[0].value;
		component.M3_temp = temp_reading;
		active_comp['M3_time'] = 'now';
		component.M3_chef_id = 0;
		url = RESTHOME + 'M3_comp.php';
	}
	let data = { data: JSON.stringify(component) };
	console.log(tag,"sent off: ", data,' to ', url);
	$.ajax({
		url: url,
		type: "POST",
		data: data,

		success: function(result) {
			console.log(tag,"result: ",result);
			console.log(tag,"component: ",component);
			if (active_comp['M2_time'] == '' && !force) { 
				// at M1 - component has tracked ingredients
				// get chef id and print labels
				var qty = document.getElementsByName('m1_label_qty')[0].value;
				console.log(tag,"At M1: printing "+qty+" labels for active_comp:", active_comp);
				print_component_labels(qty);
				document.getElementsByName('m1_label_qty')[0].value = 1;
			} else {
				console.log(tag,"At M2/3: not printing labels");
				if (component['M3_temp'] && component['M3_temp'] != '') {
					console.log(tag,'M3 finished',component['M3_temp']);
					if (force) document.getElementById('m2_temp_div_3a').innerHTML= "M3 FORCED";
					openPage('m2_temp_modal3', this, 'red','m_modal2','tabclass');
				} else {
					console.log(tag,'at M2');
					goto_m_main();
					// openPage('m2_temp_modal3', this, 'red','m_modal2','tabclass');
				}
			}
		},
		done: function(result) {
			console.log(tag, "done ",result);
		},
		fail: function (result) {
			console.log(tag, "fail ",result);
		}
	});
}

function continue_chilling()
{
	if (active_comp.milestone_ok) comp_milestone();
	else goto_m_main();
}

function check_temp_m2(t) // M2 or M3 .... or M1 if component has ingredients.
{
	let tag = "check_temp_m2: ";
	// TODO - rewrite this as a state machine. It's far to complex as it is.
	// if M1 then need to print labels and log temperature to existing record TODO
	console.log(tag,"check temp M2/3");
	active_comp.milestone = '';
	active_comp.milestone_ok = false;
	console.log(tag,"active_comp: ",active_comp);
	
	// var t = document.getElementsByName('m2_temp')[0].value;
	console.log(tag,"value of first m2_temp: ",t);
	if (t.length > 0) {
		openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
		var prep_type_id = active_comp['prep_type_id'];
		console.log('prep_type ' + prep_type_id);
		if (prep_type_id == 2)  // disable force M3 for HF - no idea why. Ordained by higher authority
		{
			document.getElementById('force_m3_btn').style.display = 'none';
		} else {
			document.getElementById('force_m3_btn').style.display = 'inline-block';
		}
		var temp_target = get_preptype_val(prep_type_id,'M1_temp');
		active_comp.milestone = 'M1';
		if (active_comp['M1_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M2_temp');
			active_comp.milestone = 'M2';
		} 
		if (active_comp['M2_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M3_temp');
			active_comp.milestone = 'M3';
		} 
		console.log(tag,'target temp: ',temp_target,t,active_comp.milestone);

		let m2_temp_div_2a = document.getElementById('m2_temp_div_2a');
		let m2_temp_div_3a = document.getElementById('m2_temp_div_3a');

		clearChildren(m2_temp_div_2a);
		clearChildren(m2_temp_div_3a);

		if (active_comp.milestone == 'M1' && parseInt(t) > parseInt(temp_target)) {
			set_barcode_mode('M1');
			m2_temp_div_2a.innerHTML= active_comp.milestone + " achieved";
			m2_temp_div_3a.innerHTML= active_comp.milestone + " achieved";
			active_comp['M1_temp'] = t;
			openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
			return;
			// comp_milestone(t);
		}
		if (parseInt(t) < parseInt(temp_target)) {
			console.log(tag,"check M2");
			temp_target = get_preptype_val(prep_type_id,'M3_temp');
			console.log(tag,'M3 temp target = ',temp_target);
			if (active_comp.milestone ==  'M3') {
				//if M3 achieved
				// milestone_achieved_box(m2_temp_div_3a,"M3");
				active_comp.milestone_ok = true;
				
				if (active_comp.remaining > 0) {
				    comp_milestone(t);
                }
			} //else
			if (active_comp.milestone ==  'M2' && temp_target != null && parseInt(t) < parseInt(temp_target)) {
				console.log(tag,"M2 achieved");
				active_comp.milestone = 'M3';
				active_comp['M2_temp'] = t;
				active_comp['M3_temp'] = t;
				active_comp['M3_time'] = 'now';
				active_comp['M2_time'] = 'now';

				if (active_comp.remaining > 0) {
					comp_milestone(t);
				}
			}
            if (active_comp.milestone ==  'M2' && temp_target == null) { // no M3 so component finished
                active_comp.finished = 'true';
                comp_milestone(t);
            }
			//if M2 achieved
			// TODO does not work
			milestone_achieved_box(m2_temp_div_2a,"M2");//add if direct skip M1 to M3
			milestone_achieved_box(m2_temp_div_3a,"M3");

			// if (active_comp.remaining > 0) comp_milestone(t);
			active_comp.milestone_ok = true;
		} else {
			openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');

			document.getElementById('m2_temp_div_2').innerHTML= "<div class='orange'>" + parseInt(t) + "&#176C</div>";
		}

		if (active_comp['M1_time'].length < 1) {
			openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
		} else if (active_comp.remaining > 0 ) {
			openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
			checkTempDiv(document.getElementById('m2_temp_div_2a'),active_comp,"goto_m_main();",true);
		} else {
			console.log("overdue - QA");
			set_barcode_mode('KQA_override');
			if (active_comp['M2_time'].length > 1) {
				document.getElementById('m2_temp_div_overdue_2').innerHTML = "<div class='red'>" + parseInt(t) + "&#176C</div>";
				openPage('m2_temp_overdue_A', this, 'red','m_modal2','tabclass');
			} else {
				openPage('m2_temp_overdue_M2A', this, 'red','m_modal2','tabclass');
			}
		}
	}
}

function clearChildren(elem){
	while (elem.lastChild) {
		elem.removeChild(elem.lastChild);
	}
}

function milestone_achieved_box(anchor, milestone){
	anchor.appendChild(document.createElement('hr'));

	let div = document.createElement('div');
	div.className = 'milestone_achieved';

	if(milestone == 'M2') {
		div.appendChild(iconM2());
	} else {
		div.appendChild(iconM3());
	}

	let txtdiv = document.createElement('div');
	txtdiv.className = "temp_achieved center";
	txtdiv.innerText = milestone + ' achieved';
	div.appendChild(txtdiv);

	anchor.appendChild(div);

	anchor.appendChild(document.createElement('hr'));
}

function active_comp_selected(id) 
{
	console.log("active_comp_selected",id);
	load_chefs(null);
	openPage('m2_temp_modal', this, 'red','m_modal2','tabclass');
	read_M2temp();

	active_comp = active_comps[id];
	console.log("active_comp_selected: Listing active components\n",active_comp);

	openPage('m_temp', this, 'red','mobile_main','tabclass');
	
	var prep_type_id = active_comp['prep_type_id'];
	console.log('prep_type_id',prep_type_id);
	if (prep_type_id < 1) {prep_type_id = 1}
	// var prep_type_val = get_preptype_val(prep_type_id,'M2_temp');

	document.getElementById('chk_temp_item_div').innerHTML = active_comp['description'];
	// document.getElementById('chk_temp_item_id_div').innerHTML = sprintf('C01%06d',active_comp['id']);
	document.getElementById('chk_temp_item_id_div').innerHTML = 'C01000' + active_comp['id'];
	
	var milestone_due = 'NA';
	var remaining = 0;
	var milestone_temp = "NA";
	var target_temp = "NA";
	var M1_time = new Date(active_comp['M1_time']);
	var M2_time = new Date(active_comp['M2_time']);

	console.log("M2 time -",active_comp['M2_time'],"-");

	var now = new Date();
	var now_ms = now.getTime();
	var M1_ms = M1_time.getTime(); // time in millisecs

	if (active_comp['M1_time'] == '') {
		milestone_due = 'M1';
		target_temp = " > " + get_preptype_val(prep_type_id,'M1_temp');
	} else {
		if (active_comp['M2_time'] == '') {
			milestone_due = 'M2';
		} else {
			milestone_due = 'M3';
		}
		var due_min = get_preptype_val(prep_type_id,milestone_due+'_time_minutes');
		var due_ms = M1_ms + due_min * 60 * 1000;  			
		remaining = (due_ms - now_ms) / (60 * 1000);
		console.log(milestone_due+"_due_min M1_ms",due_min,M1_ms,due_ms,format_minutes(remaining));
		target_temp = " < " + get_preptype_val(prep_type_id,milestone_due+'_temp');
	}

	document.getElementById('ms_1').innerHTML = milestone_due;
	active_comp.remaining = remaining;
	if (milestone_due != 'M1') {
		var tt = (remaining >= 0) ? " REMAINING" : " OVERDUE";
		document.getElementById('ms_1_text').innerHTML = format_minutes(remaining) + tt;
	} else {
		document.getElementById('ms_1_text').innerHTML = "";
	}
	document.getElementById('ms_2').innerHTML = milestone_due;
	document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
	document.getElementById('ms_2_target').innerHTML = target_temp + "&#176;";

	document.getElementById('chk_temp_pt_div').innerHTML = get_preptype_val(prep_type_id,'code');

	checkTempDiv(document.getElementById('m2_temp_div'), active_comp, "read_M2temp()");
}

function reprint_comp_labels()
{
	console.log('reprint_comp_labels');
	openPage('m_reprint_labels', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	// show('kitchen_manual_code');
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
		success: function (result) {
			active_comps = result;
			// document.getElementById('active_comps').innerHTML = result;
			//        console.log("got " + result.length + " comps");
			//    m_show_active_components(result,true);

		},
		done: function (result) {
			console.log("done load_comps ");
		},
		fail: (function (result) {
			console.log("fail load_comps", result);
		})
	});
}


function m_tracking()
{
	console.log('goto_active_components');
	load_comps();
	$('#search').val('');
	openPage('m_current_tracking', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	// show('kitchen_manual_code');
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
		success: function (result) {
			active_comps = result;
			m_show_active_components(result, false);
		},
		done: function (result) {
			console.log("done load_comps ");
		},
		fail: (function (result) {
			console.log("fail load_comps", result);
		})
	});
}

function get_comps_for_plating(item)
{
	let tag = 'get_comps_for_plating: ';
	console.log(tag,item);
	$.ajax({
		url: RESTHOME + "get_active_comps.php?finished=true",
		type: "POST",
		dataType: 'json',
		success: function (result) {
			plating_comps = result;
			console.log(tag,"got " + result.length + " comps for plating");
			do_show_menu_item_components(item);
		},
		done: function (result) {
			console.log(tag,"done");
		},
		fail: (function (result) {
			console.log(tag,"fail", result);
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
		success: function (result) {
			//  active_comps = result;
			// clear plating teams;
			if (plating_teams != null) {
				for (var i = 0; i < plating_teams.length; i++) {
					if (plating_teams[i] != null) plating_teams[i] = [];
				}

				console.log("got " + result.length + " plating_teams");
				for (var i = 0; i < result.length; i++) {
					console.log("pt", result[i].user_id, result[i].team_id);
					var chef = get_chef_by_id(result[i].user_id);
					if (chef) {
						console.log("found chef ", chef['label']);
						if (plating_teams[result[i].team_id] == null) {
							plating_teams[result[i].team_id] = [];
						}
						plating_teams[result[i].team_id].push(chef);
					} else {
						console.log("could not find chef");
					}
					//plating_teams[result[i].team_id].push(get_chef_by_id(result[i].user_id));
					// plating_teams[active_plating_team].push(chefs[idx]);
				}
			}


		},
		fail: (function (result) {
			console.log("fail load_comps", result);
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
	clearChildren(document.getElementById('ms_1'));
	clearChildren(document.getElementById('ms_1_text'));
	clearChildren(document.getElementById('ms_2'));
	clearChildren(document.getElementById('ms_2_text'));
	clearChildren(document.getElementById('ms_2_target'));
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
	let tag = 'print_component_labels: ';
	console.log(tag,qty, 'active_comp', active_comp);
	let comp = active_comp;
	comp.copies = qty;
	if (!comp.preparedBy) {
		console.log(tag, "comp: ", comp);
		let chef = get_chef_by_id(comp['M1_chef_id']);
		if (chef) {
		        comp.preparedBy = chef['label'];
		} else {
			console.log(tag, "found no chef");
			comp.preparedBy = "ERROR" ; // should be set by now
		}
	}
	
	let data = {data: JSON.stringify(comp)};
	console.log(tag, "sent off: ", data, "comp: ",comp);

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
}

// TODO - come up with a sensible naming system for groups of functions
function reprint_supplier_labels()
{
	console.log('reprint_supplier_labels');
	set_barcode_mode('dock_reprint'); // callback to reprint_doc_labels
	hide('dock_search_div');
}

function reprint_dock_labels(cid)
{
	let tag = 'reprint_dock_labels: ';
	console.log(tag,cid);
	load_chefs(null);
	document.getElementById('drl_details_div').innerHTML = cid;
	// set_barcode_mode('dock_reprint');
	$.ajax({
        url: RESTHOME + "get_active_comps.php?cid=" + cid,
        type: "POST",

        success: function(result) {
        	console.log(tag,result);
        	
        	var comps = JSON.parse(result);
            if (comps) {
            	active_comp = comps[0];
            	let h = "<b>" + active_comp.description + "</b><br>";
            	h += "USE BY: " + active_comp.expiry_date;
            	document.getElementById('drl_details_div').innerHTML = h;
            	openPage('m_dock_reprint1', document.getElementById('s_reprint_labels_tab'), 'red','m_modal','m_top_menu',null);
            	console.log(tag,"got component " + active_comp.description);
            	set_barcode_mode('dock_reprint');
            }
            else {
            	console.log(tag,'could not find incredient');
            	set_barcode_mode('dock_reprint');
            }
            
        },
        fail: (function (result) {
            console.log(tag,"fail ",result);
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

function get_active_comp_by_id(cid)
{
	for (var i = 0; i < active_comps.length; i++) {
		if (active_comps[i].id == cid) {
			return (active_comps[i]);
		}
	}
}

function reprint_active_comp_labels(id)
{
	active_comp = get_active_comp_by_id(id); // get component
	if (!active_comp) {
		console.log('cant find component id',id );
		return;
	}
	clear_comp_fields();
	document.getElementById('chk_temp_item_div').innerHTML = active_comp['description'];
	//document.getElementById('chk_temp_item_id_div').innerHTML = sprintf('C01%06d',active_comp['id']);
	document.getElementById('chk_temp_item_id_div').innerHTML = 'C01000' + active_comp['id'];
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
	let tag = "m_show_active_components: ";

	display_real_time();
	var timeout_msg = null;

	var div = document.getElementById('m_current_tracking');
	if (reprint) div = document.getElementById('m_reprint_labels');

	if (data.length < 1) {
		div.innerHTML = "<span>No Active Components</span>";
		return;
	}

	clearChildren(div);

	//build table
	var tab = new_node('table','','component_table');
	//build head
	var thead = new_node('thead');
	var tr = new_node('tr');

	tr.appendChild(new_node('th', 'Description', 'comp_left'));
	if (!reprint) {
		tr.appendChild(new_node('th', 'M', 'comp_middle'));
		tr.appendChild(new_node('th', 'TIME', 'comp_right'));
	}

   	thead.appendChild(tr);
	tab.appendChild(thead);

	//body
	var tbody = new_node('tbody');

	for (let i=0; i<data.length; ++i) {
   		tr = document.createElement('tr');

   		var clickdiv;
   		var span_txt='';

   		if(reprint){
   			// online version
			if (typeof(serial) == 'undefined'){
				tr.setAttribute(
					"onclick",
					"reprint_active_comp_labels(" + data[i]['id'] + ");"
				);
			}

			clickdiv = "<div>";
		}else{
			/* if (typeof(serial) == 'undefined') */ 
			tr.setAttribute("onclick",'active_comp_selected(' + i + ');');
		//	span_txt = "<span class='hidden'>" + data[i]['id'] + "</span>";

			clickdiv = "<div class='tooltip'>";
		}

		clickdiv += data[i]['description']+span_txt+ "</div>";

   		tr.appendChild(new_td(clickdiv,'comp'));

	//	console.log("prep_type_id",prep_type_id);
		console.log(tag, 'checking ',data[i].description,data[i].state);
		if (reprint) {
			tbody.appendChild(tr);
   		}else{
			var M1_time = new Date(data[i]['M1_time']);
			var M2_time = new Date(data[i]['M2_time']);
			var prep_type_id = data[i]['prep_type_id'];
			//	console.log("M2 time -",data[i]['M2_time'],"-");
			var remaining = 0;
			var now = new Date();
			var now_ms = now.getTime();
			var M1_ms = M1_time.getTime(); // time in millisecs

			let num;
			let status_msg;

			let push_to_top = false;
			if (data[i]['M1_time'] == '') { 
				// M0 - ingredients have been selected
				num = '1';
				status_msg = new_td('Cooking','comp');
			} else {
				let due_min;
				if (data[i]['M2_time'] == '') {
					num = '2';
					due_min = get_preptype_val(prep_type_id,'M2_time_minutes');
				} else {
					num = '3';
					due_min = get_preptype_val(prep_type_id,'M3_time_minutes');
				}

				// 60000 = 60 (seconds in minute) * (1000 ms in sec)
				let due_ms = M1_ms + due_min * 60000;
				remaining = (due_ms - now_ms) / 60000;

				if (remaining > 0) {
					status_msg = new_td(format_minutes(remaining) + "",'comp');
				} else {
					push_to_top = true;

					//set popup
					if (timeout_msg == null) {timeout_msg = '<h2>OVERDUE</h2>';}
					timeout_msg += data[i]['description'] + ' : ' + format_minutes(Math.abs(remaining))+'<br />';

					//set line message
					status_msg = new_td(format_minutes(Math.abs(remaining)) + " overdue",'comp red');
				}
			}

			tr.appendChild(new_td('<div class="m_bluedot">'+num+'</div>','comp'));
	   		tr.appendChild(status_msg);

			if(push_to_top){
				// overdue? push to beginning of list
				tbody.insertBefore(tr, tbody.childNodes[0]);
			}else{
				tbody.appendChild(tr);
			}
		}
	}
	tab.appendChild(tbody);
   	div.appendChild(tab);
   	if (timeout_msg != null && mode == 'kitchen' && document.getElementById('m_current_tracking').style.display == 'block') {
   		console.log('timeout ' + timeout_msg);
   		console.log('active items',document.getElementById('m_current_tracking').style.display);
   		popup_timeout(timeout_msg);
   	}
}

function new_node(type,content='',classname=''){
	var node = document.createElement(type);
	if(content!=='')
		node.className = classname;
	if(classname!=='')
		node.innerHTML = content;
	return(node);
}

function goto_select_team()
{
	console.log('goto_select_team');

	openPage('m_sel_team_members', this, 'red','m_modal','tabclass');
	show_plating_team();
}

function show_chef_select()
{
	var s = document.getElementById('sel_team_member');
	s.innerHTML = null;
	if (typeof(serial) == 'undefined') {
		var select = document.createElement('select');
		select.name = 'sel_chef';
		// console.log('found plating teams ',plating_teams.length);
		var pt = plating_teams[active_plating_team];
		
		for (var i = 0; i < chefs.length; i++) {
			var found = false;
			for (var j = 0; j < pt.length; j++) {
				if (pt[j]['id'] == chefs[i]['id']) found = true;
			}
			if (!found) {
				option = document.createElement( 'option' );
				option.value = i;
				option.textContent =  chefs[i]['label'];
				select.appendChild( option );
			}
		}
		s.appendChild(select);
	}
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
			var d = "<div class='plating_team'>" + pt[i]['label'];
			d += "<div class='del' onclick='rem_pt_mem(" + active_plating_team + "," + i + ");'>&#x02A2F;</div>";
			d += "</div>";
			l.innerHTML += d;
		}
	}
	show_chef_select();
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
		if (plating_items[i].menu_item_id == menu_item_id) {
			return (plating_items[i]);
		}
	}
	return(null);
}

function get_plating_item_by_id(id)
{
	if (typeof(plating_items) != 'undefined') {
		for (var i = 0; i < plating_items.length; i++) {
			if (plating_items[i].id == id) {
				return (plating_items[i]);
			}
		}
	}
	return(null);
}

function load_comps(fn)
{
	let tag = 'load_comps: ';
	console.log(tag,"loading menu item components");
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
        			} else {
        				document.getElementById('new_comp_btns').style.display = 'none';
        			}
        		},
        		select: function(event, ui) {
                    // place the person.given_name value into the text field called 'select_origin'...
                    $('#search').val(ui.item.label);
                    // and place the person.id into the hidden text field called 'link_origin_id'.
                 	console.log(tag,'selected ',ui.item.value);
                 	component_selected(ui.item.value);
                 	$('#search').val(''); // this might go wrong
                 	// cordova.plugins.Keyboard.close();
                 	$('#search').blur();
                    return false;
                }  
            });
            console.log(tag,"got " + result.length + " comps");
            if (typeof(fn) == 'function') fn();
        },
        done: function(result) {
            console.log(tag,"done ",result);
        },
        fail: (function (result) {
            console.log(tag,"fail ",result);
        })
    });
}

function new_td(content,classname,inner_class = 'm-10') {
	var td = document.createElement('td');
	td.className = classname;
	td.innerHTML = "<div class='" + inner_class + "'>" + content + "</div>";
	return(td);
}

function iconM2(){
	return new_img("img/icon_M2.svg");
}

function iconM3(){
	return new_img("img/icon_M3.svg");
}

function iconProbe(){
	return new_img("img/icon_Probe.svg","icon_Probe");
}

function iconIR(){
	return new_img("img/icon_IR.svg","icon_IR");
}

function icon_pass(){
	return new_img("img/icon_pass.svg","icon_pass");
}

function icon_fail(){
	return new_img("img/icon_fail.svg","icon_fail");
}

function new_img(source,classname = "") {
	var img = document.createElement('img');
	img.className = classname;
	img.src = source;
	return(img);
}

var refresh_count = 0;
function refresh_times()
{
	console.log("time!");
	load_tracking_data();
	setTimeout(refresh_times,60 * 1000);
	document.getElementById('battery_div').innerHTML = " - " + refresh_count++;
	get_internal_battery_voltage();
	qpack_resume();
}

function search_suppliers()
{
	console.log('search_suppliers');
}

