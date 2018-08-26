var default_tab='m_current_tracking_tab';
var comps = null;
var plating_comps = null; // components in cool room
var preptypes = null;
var menu_items = null;
var plating_teams = null;
var plating_item = null;
var active_plating_team = 0;
var active_comp = null; // the component currently being worked on
var active_menu_item_id = null;
var new_comp = null; // start a new component - M1
var chefs = null;
var RESTHOME = "http://10.0.0.32/acs/REST/";
var plating_items = null; // array of menu_items currently plating
var barcode_mode = null;

(function(){
    // Convert array to object
    var convArrToObj = function(array){
        var thisEleObj = new Object();
        if(typeof array == "object"){
            for(var i in array){
                var thisEle = convArrToObj(array[i]);
                thisEleObj[i] = thisEle;
            }
        }else {
            thisEleObj = array;
        }
        return thisEleObj;
    };
    var oldJSONStringify = JSON.stringify;
    JSON.stringify = function(input){
        if(oldJSONStringify(input) == '[]')
            return oldJSONStringify(convArrToObj(input));
        else
            return oldJSONStringify(input);
    };
})();

function set_barcode_mode(mode)
{
	barcode_mode = mode;
	keyboard_str = '';
}
function process_barcode(s)
{ 
	console.log("process_barcode " + s + " mode " + barcode_mode);
	if (barcode_mode == null) {
		return;
	}
	if (s.indexOf('u') >= 0) { // user barcode scanned
		var uid = parseInt(s.substring(4));
		if (barcode_mode == 'login') {
			login(uid);
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
	if (s.indexOf('c') >= 0) { // component barcode scanned
		var cid = parseInt(s.substring(5));
		if (barcode_mode = 'PT_item') {
			plating_comp_barcode_scanned(cid);
		}
	}
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
	openPage('plating_div', this, 'red','mobile_main','tabclass');
	openPage('m_sel_team', this, 'red','m_modal','tabclass');
	document.getElementById('plating_comment_div').innerHTML = '';
}

function save_plating_team()
{
	var data =  {data: JSON.stringify(plating_teams)};
	console.log("Sent Off: %j", data);
	barcode_mode = null;
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


function goto_plating()
{
	
	// ???? load_menu_items();
	show('plating_return');
	hide('plating_print_labels');
	if (active_plating_team == null) {
		goto_plating_teams();
	}
	openPage('m_plating', this, 'red','mobile_main','tabclass');
	openPage('m_plating_sched', this, 'red','m_modal','tabclass');
	var hd = document.getElementById('active_plating_team_head');
	hd.innerHTML = "Plating Team " + active_plating_team;
	var t = document.getElementById('plating_sched_list');
	t.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'plating_tab';
	var tr = document.createElement('tr');
	tr.className = 'plating_tab';
	var th = document.createElement('th');
	th.innerHTML = 'CODE';
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML = 'PRODUCT NAME';
	tr.appendChild(th);
	tab.appendChild(tr);
	for (i = 0; i < menu_items.length; i++) {
		if (menu_items[i]['plating_team'] == active_plating_team) {
			tr = document.createElement('tr');
			var td = document.createElement('td');
			td.innerHTML = menu_items[i]['code'];
			tr.appendChild(td);
			td = document.createElement('td');
			var div = "<div onclick='show_menu_item_components(" + menu_items[i]['id'] + ");'>" + menu_items[i]['dish_name']; + "</div>"
			// td.innerHTML = menu_items[i]['dish_name'];
			td.innerHTML = div;
			tr.appendChild(td);
			tab.appendChild(tr);
		}
	}
	t.appendChild(tab);
}

function get_menu_item_by_id(menu_item_id) {
	// menu_items not a hashed array because the search fn needs it that way
	for (var i = 0; i < menu_items.length; i++) {
		if (menu_items[i].id == menu_item_id) return(menu_items[i]);
	}
	return(null);
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
			console.log("plating_comp_barcode_scanned found " + plating_comps[i].description);
			description = plating_comps[i].description;
		}
	}
	var items = plating_item.items;
	console.log("now checking plating item " + items.length);
	for (var i = 0; i < items.length; i++) {
		if (items[i].description == description) {
			items[i].checked = true;
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
	plating_item.items[plating_item.active_item].M1_temp = temperature;
	goto_active_plating();
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
	plating_item = Object.create(get_menu_item_by_id(menu_item_id)); 
	plating_item.menu_item_id = menu_item_id;
	plating_items.push(plating_item);
	console.log(plating_item);
	return(plating_item);
}

function print_plating_labels(qty)
{
	
	console.log(" print_plating_labels ",qty);

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
function do_show_menu_item_components(menu_item_id)
{
	barcode_mode = "PT_comp";
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
	th.innerHTML= plating_item.dish_name + "<br>" + plating_item.code;
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML='S/L';
	tr.appendChild(th);
	th = document.createElement('th');
	th.innerHTML='TEMP';
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
				var clickdiv = "<div onclick='plating_comp_selected(" + i + ");'>" + items[i].description + "</div>";
				// show items in coolroom ready to be plated
			// 	clickdiv += show_plating_comps(items[i].description);
				// tr.appendChild(new_td(items[i].description,'item'));
				tr.appendChild(new_td(clickdiv,'item'));
				var td = document.createElement('td');
				td.id = 'plating_item_checked_' + i;
				td.innerHTML = '-';
				if (items[i].checked) {
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
		}
		else {
			show('plating_return');
			hide('plating_print_labels');
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
	openPage('m_search', this, 'red','m_modal','tabclass');
}
function goto_m_main()
{
	openPage('mm2', this, 'red','mobile_main','tabclass');
	m_tracking();
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

// called when user searchs for and selects a component - M1 only 
function component_selected(id)
{
	console.log("component selected - loading chefs");
	load_chefs(null);
	new_comp = get_component_by_id(id);
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
	if (M1_temp == null) { // low risk. No temp required
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

function read_plating_M1temp(callback){
	
	// document.getElementById('m1_temp_div').innerHTML = '';
	read_temp('M1_plating');
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
	for (i = 0; i < chefs.length; i++) {
		option = document.createElement( 'option' );
		option.value = chefs[i]['id'];
		option.textContent =  chefs[i]['label'];
		select.appendChild( option );
		// console.log(i);
	}
	s.appendChild(select);
	
}

function start_component()
{
	load_chefs(add_chef_select('m1_temp_div_chef','m1_chef_id'));
	var component = new Object();
	component.description = new_comp['description']; // simplifies display
	active_comp = Object.assign({}, new_comp);
	component.comp_id = new_comp['id'];
	component.prep_type = new_comp['prep_type'];
	component.shelf_life_days = new_comp.shelf_life_days;
	prep_type_id = component.prep_type;
	console.log("start compontent " + component.description + " prep_type" + component.prep_type);
	// component.M1_temp = document.getElementsByName('m1_temp')[0].value;
	var M2_time = get_preptype_val(prep_type_id,'M2_time_minutes');
	console.log("At M1, M2 time = " + M2_time);
	console.log("At M1, M3 time = " + get_preptype_val(prep_type_id,'M3_time_minutes'));
	if (M2_time == null) {
		component.finished = 'true';
	}
	else {
		component.M1_temp = new_comp['M1_temp'];
	}
	
	component.M1_chef_id = document.getElementsByName('m1_chef_id')[0].value;
	
	if (component.M1_chef_id < 1) component.M1_chef_id = 1;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    document.getElementsByName('m1_chef_id')[0].value = '';
   //  document.getElementsByName('m1_temp')[0].value = '';
    $.ajax({
        url: RESTHOME + "new_comp.php",
        type: "POST",
        data: data,

        success: function(result) { // need to get the id of the new component back to print labels
            console.log("start_component result ",result);
            var comp = JSON.parse(result);
            console.log("start_component id =  ",comp.id);
            var qty = document.getElementsByName('m1_label_qty')[0].value;
            active_comp.id = comp.id;
            print_component_labels(qty);
            document.getElementsByName('m1_label_qty')[0].value = 1;
            goto_m_main();
        },
        done: function(result) {
            console.log("done start_component result ",result);
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
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
	var prep_type_id = active_comp['prep_type_id'];
	
	var temp_target = get_preptype_val(prep_type_id,'M2_temp');
		
	var component = new Object();
	component.id = active_comp['id'];
	var url = '';
	if (active_comp['M2_time'] == '') { // M2
		// component.M2_temp = document.getElementsByName('m2_temp')[0].value;
		component.M2_temp = temp_reading;
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
		component.M3_chef_id = 0;
		url = RESTHOME + 'M3_comp.php';
	}
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: url,
        type: "POST",
        data: data,

        success: function(result) {
            console.log("start_component result ",result);
            if (active_comp['M2_time'] == '') { 
            	// goto_m_main();
            }
            else {
            	openPage('m2_temp_modal3', this, 'red','m_modal2','tabclass');
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

function check_temp_m2(t) // M2 or M3
{
	console.log("check temp M2/3");
	console.log(active_comp);
	
	// var t = document.getElementsByName('m2_temp')[0].value;
	console.log("check temp",t);
	if (t.length > 0) {
		openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
		var prep_type_id = active_comp['prep_type_id'];
		
		var temp_target = get_preptype_val(prep_type_id,'M2_temp');
		var milestone = 'M2';
		if (active_comp['M2_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M3_temp');
			milestone = 'M3';
		} 
		console.log('check_temp_m2 target temp',temp_target,t);
		
		document.getElementById('m2_temp_div_2').innerHTML=parseInt(t) + "&#176C"
		document.getElementById('m2_temp_div_3').innerHTML=parseInt(t) + "&#176C"
		if (parseInt(t) < parseInt(temp_target)) {
			document.getElementById('m2_temp_div_2a').innerHTML= milestone + " achieved";
			document.getElementById('m2_temp_div_3a').innerHTML= milestone + " achieved";
			comp_milestone(t);
			
		}
		else {
			// document.getElementById('m2_temp_div_2a').innerHTML= milestone + "";
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
	if (active_comp['M2_time'] == '') {
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
	if (remaining >= 0) {
		document.getElementById('ms_1_text').innerHTML = format_minutes(remaining) + " REMAINING";
	}
	else {
		document.getElementById('ms_1_text').innerHTML = format_minutes(remaining) + " OVERDUE";
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
	 $.ajax({
	        url: RESTHOME + "get_active_comps.php?all=true",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            console.log("got " + result.length + " comps");
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
	 $.ajax({
	        url: RESTHOME + "get_active_comps.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            console.log("got " + result.length + " comps");
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

function get_chef_by_id(id)
{

	for (var i = 0; i < chefs.length;i++) {
		if (chefs[i].id == id) return chefs[i];
	}
	return(null);
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
	var qty = document.getElementsByName('m1_repeat_label_qty')[0].value;
	if (qty && qty > 0) print_component_labels(qty);
}
function print_component_labels(qty)
{
	console.log(" print_component_labels ",qty);

	var comp = Object.assign({}, active_comp);
	comp.copies = qty;
	
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
	goto_m_main();
}

function reprint_active_comp_labels(id)
{
	active_comp = active_comps[id];
	clear_comp_fields();
	document.getElementById('chk_temp_item_div').innerHTML = active_comp['description'];
	openPage('m_reprint_modal4', this, 'red','m_modal2','tabclass');
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	
	
}

function m_show_active_components(data,reprint)
{
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
    tr.appendChild(new_td('M','comp'));
    
    tr.appendChild(new_td('TIME','comp'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		
   		var clickdiv = "<div onclick='active_comp_selected(" + i + ");'>" + data[i]['description'] + "</div>";
   		if (reprint) clickdiv = "<div onclick='reprint_active_comp_labels(" + i + ");'>" + data[i]['description'] + "</div>";
   		// tr.appendChild(new_td(data[i]['description'],'comp'));
   		tr.appendChild(new_td(clickdiv,'comp'));
   		
   		var M1_time = new Date(data[i]['M1_time']);
   		var M2_time = new Date(data[i]['M2_time']);
   		var prep_type_id = data[i]['prep_type_id'];
   		console.log("M2 time -",data[i]['M2_time'],"-");
   		var remaining = 0;
   		var now = new Date();
		var now_ms = now.getTime();
		var M1_ms = M1_time.getTime(); // time in millisecs
		console.log("prep_type_id",prep_type_id);
		if (reprint) {
   			
   		}
   		else {
	   		if (data[i]['M2_time'] == '') {
	   			var M2_due_min = get_preptype_val(prep_type_id,'M2_time_minutes');
	   			var M2_due_ms = M1_ms + M2_due_min * 60 * 1000;  			
	   			remaining = (M2_due_ms - now_ms) / (60 * 1000);
	   			console.log("M2_due_min M1_ms",M2_due_min,M1_ms,M2_due_ms,format_minutes(remaining));
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
	   			tr.appendChild(new_td(format_minutes(Math.abs(remaining)) + " overdue",'comp'));
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
	for (i = 0; i < menu_items.length; i++) {
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
	for (i = 0; i < plating_teams.length; i++) {
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

function load_chefs(fn)
{
	console.log("loading chefs");
    $.ajax({
    	url: RESTHOME + "get_chefs.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
            chefs = result;
            
            if (fn) {
            	console.log("calling fn");
            	fn();
            }
            load_plating_teams();
            add_chef_select('m1_temp_div_chef','m1_chef_id');
            console.log("got " + result.length + " chefs");   
            
        },
        fail: (function (result) {
            console.log("fail load_chefss",result);
        })
    });
}

function goto_select_team()
{
	console.log('goto_select_team');
	var s = document.getElementById('sel_team_member');
	s.innerHTML = null;
	var select = document.createElement('select');
	select.name = 'sel_chef';
	// console.log('found plating teams ',plating_teams.length);
	for (i = 0; i < chefs.length; i++) {
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
	for (i = 0; i < pt.length; i++) {
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
		barcode_mode = 'PT';
		console.log("Team " + active_plating_team);
		load_chefs(goto_select_team);
		document.getElementById('plating_comment_div').innerHTML = "Plating Team " + active_plating_team;
	}
}
function load_menu_items()
{	
	console.log("loading menu items");
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


function load_comps()
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
	td.innerHTML = content;
	return(td);
}

function show_time(d)
{
	options = {
		hour: 'numeric', minute: 'numeric',

	};
	return (new Intl.DateTimeFormat('en-AU', options).format(d));
}
function show_date(d)
{
	options = {
		day: 'numeric', month: 'numeric',year: 'numeric',

	};
	return (new Intl.DateTimeFormat('en-AU', options).format(d));
}

function get_component_by_id(id)
{
	for(i= 0; i < comps.length; i++) {
		if (comps[i].id == id) return (comps[i]);
	}
	return null;
}
