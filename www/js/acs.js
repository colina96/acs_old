var default_tab='m_current_tracking_tab';
var comps = null;
var preptypes = null;
var active_comp = null; // the component currently being worked on
var new_comp = null; // start a new component - M1
var RESTHOME = "http://10.0.0.32/acs/REST/";

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

function goto_comp_search()
{
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
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	openPage('m_temp_modal', this, 'red','m_modal2','tabclass');
	new_comp = get_component_by_id(id);
	console.log(new_comp);
	var prep_type_id = new_comp['prep_type'];
	console.log('prep_type_id',prep_type_id);
	if (prep_type_id < 1) prep_type_id = 1;
	var prep_type_val = get_preptype_val(prep_type_id,'M1_temp');
	document.getElementById('chk_temp_item_div').innerHTML = new_comp['description'];
	document.getElementById('ms_1').innerHTML = '';
	document.getElementById('ms_1_text').innerHTML = '';
	document.getElementById('ms_2').innerHTML = 'M1';
	document.getElementById('ms_2_text').innerHTML = 'REQUIRED ';
	document.getElementById('ms_2_target').innerHTML = '> ' + get_preptype_val(prep_type_id,'M1_temp') + "&#176";

	document.getElementById('chk_temp_pt_div').innerHTML = get_preptype_val(prep_type_id,'code');
}

function check_temp() // start a new component
{
	console.log("check temp");
	console.log(new_comp);
	var t = document.getElementsByName('m1_temp')[0].value;
	var prep_type_id = new_comp['prep_type'];
	var M1_temp_target = get_preptype_val(prep_type_id,'M1_temp');
	document.getElementById('m1_temp_div_2').innerHTML=parseInt(t) + "&#176C"
	document.getElementById('m1_temp_div_3').innerHTML=parseInt(t) + "&#176C"
	document.getElementById('m1_temp_div_4').innerHTML=parseInt(t) + "&#176C"
	console.log("check temp",t,M1_temp_target);
	if (t.length > 0) {
		if (parseInt(t) < parseInt(M1_temp_target)) {
			console.log("M1 temp too low");
			openPage('m_temp_modal2', this, 'red','m_modal2','tabclass');
		}
		else {
			openPage('m_temp_modal3', this, 'red','m_modal2','tabclass');
		}
	}
	
}

function start_component()
{
	var component = new Object();
	component.description = new_comp['description'];
	component.prep_type = new_comp['prep_type'];
	component.M1_temp = document.getElementsByName('m1_temp')[0].value;
	component.M1_chef_id = document.getElementsByName('m1_chef_id')[0].value;
	if (component.M1_chef_id < 1) component.M1_chef_id = 1;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    document.getElementsByName('m1_chef_id')[0].value = '';
    document.getElementsByName('m1_temp')[0].value = '';
    $.ajax({
        url: RESTHOME + "new_comp.php",
        type: "POST",
        data: data,

        success: function(result) {
            console.log("start_component result ",result);
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
function comp_milestone()
{
	var M2_temp_reading = parseInt(document.getElementsByName('m2_temp')[0].value);
	
		// send data to REST interface
		
		var component = new Object();
		component.id = active_comp['id'];
		var url = '';
		if (active_comp['M2_time'] == '') { // M2
			component.M2_temp = document.getElementsByName('m2_temp')[0].value;
			component.M2_chef_id = 0;
			url = RESTHOME + 'M2_comp.php';
		}
		else {
			component.M3_temp = document.getElementsByName('m2_temp')[0].value;
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

function check_temp_m2() // M2 or M3
{
	console.log("check temp M2/3");
	console.log(active_comp);
	var t = document.getElementsByName('m2_temp')[0].value;
	console.log("check temp",t);
	if (t.length > 0) {
		var prep_type_id = active_comp['prep_type_id'];
		
		var temp_target = get_preptype_val(prep_type_id,'M2_temp');
		var milestone = 'M2';
		if (active_comp['M2_time'].length > 1) {
			temp_target = get_preptype_val(prep_type_id,'M3_temp');
			milestone = 'M3';
		} 
		console.log('check_temp_m2 target temp',temp_target,t);
		
		document.getElementById('m2_temp_div_2').innerHTML=parseInt(t) + "&#176C"
		if (t < temp_target) {
			document.getElementById('milestone_div_2').innerHTML= milestone + " achieved";
			document.getElementById('milestone_div_3').innerHTML= milestone + " achieved";
			comp_milestone();
			if (milestone == 'M2') {
				
			}
			else {
				
			}
		}
		else {
			document.getElementById('milestone_div_2').innerHTML= milestone + "";
		}
		openPage('m2_temp_modal2', this, 'red','m_modal2','tabclass');
	}
	
}


function active_comp_selected(id) 
{
	console.log("active_comp_selected",id);
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
	            m_show_active_components(result);
	            
	        },
	        done: function(result) {
	            console.log("done load_comps ");
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

function m_show_active_components(data)
{
	var div = document.getElementById('m_current_tracking');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	div.innerHTML = "<h1>Active Components</h1>";
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	
	
    tr.appendChild(new_td('Description','comp'));   
    tr.appendChild(new_td('M','comp'));
    
    tr.appendChild(new_td('TIME','comp'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		
   		var clickdiv = "<div onclick='active_comp_selected(" + i + ");'>" + data[i]['description'] + "</div>"
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
   		
   		// tr.appendChild(new_td(data[i]['M1_time'],'comp'));
   		
   		  		tab.appendChild(tr);
    }
   	div.appendChild(tab);
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
