<div class='acs_main'>
	
		<div class="acs_sidebar">
		  
		  <div class='acs_menu_space' >SELECT AN ACTION</div>
		  <button type='button' class='acs_comp_btn' href="#" id="new_comp_btn"  
		  	onclick="goto_start_new_comp()">START NEW</button>
		  <button type='button' class='acs_comp_btn' href="#" id="active_comps_btn" 
		  	onclick="goto_active_components()">ACTIVE COMPONENTS</button>
		  
		  
		</div>
	
		<div class="acs_right_content">
			<div id='new_comp' class='comp_details'><h1>Start new component</h1>
			<table width='100%'<tr>
				<td>Search for component: <td><input type="text" id="search" width:'100%' size='50' name='new_comp_desc'>
				<input type=hidden name='new_comp_prep_type' value=0>
				<tr><td>Prep type<td><div id='new_comp_prep_type_code'></div>				
				
				<tr>
				<td>Temperature : <td><input type="text" name="M1_temp" >
				<tr><td>Chef: <td><?php select_chef('M1_chef_id') ?>
				<tr>
				<td>Number of labels : <td><input type="text" name="M1_labels" >
				</table>
				<button type='button' class='acs_comp_btn' href="#" id="new_comp_select"  
		  	onclick="start_component()">START NEW</button>
			</div>
			<div id='active_comps' class='comp_details menu_details_active'>
			</div>
			<div id='expired_menus' class='comp_details'>
			</div>
			<div class='acs_modal' id='comp_action_modal_M2'>
				<div class='modal_header'><span><h1>M2</h1></span>
				<div class='acs_container'><div id='comp_modal_title_div'></div></div>
				<div class='close_modal' onclick='close_menu_component_modal("comp_action_modal_M2");'>X</div>
				</div>
			
				<div id='comp_action_body_div'>
					<table width='100%'>
					<tr><td>Temperature : <td><input type="text" name="M2_temp" size='3'>
					<td><div id='M2_target_temp'></div></td>
					<tr><td colspan=2>Chef: <td><?php select_chef('M2_chef_id') ?>
					<tr>
				</table>
				<div id='comp_M2_correct' class='acs_container'></div>
				<button type='button' class='acs_comp_btn' href="#" id="comp_M2_action"  
		  				onclick="comp_M2()">SUBMIT</button>
		  		<button type='button' class='acs_delete_btn' href="#" id="delete_comp_action"  
		  				onclick="delete_comp()">DELETE</button>
				</div>
			</div>
			<div class='acs_modal' id='comp_action_modal_M3'>
				<div class='modal_header'><span><h1>M3</h1></span>
				<div class='acs_container'><div id='comp_modal_M3_title_div'></div></div>
				<div class='close_modal' onclick='close_menu_component_modal("comp_action_modal_M3");'>X</div>
				</div>
			
				<div id='comp_action_body_M3_div'>
					<table width='100%'>
					<tr><td>Temperature : <td><input type="text" name="M3_temp" size='3'>
					<td><div id='M3_target_temp'></div></td>
					<tr><td colspan=2>Chef: <td><?php select_chef('M3_chef_id') ?>
					<tr>
				</table>
				<div id='comp_M3_correct' class='acs_container'></div>
				<button type='button' class='acs_comp_btn' href="#" id="comp_M3_action"  
		  				onclick="comp_M3()">SUBMIT</button>
		  		<button type='button' class='acs_delete_btn' href="#" id="delete_comp_action"  
		  				onclick="delete_comp()">DELETE</button>
				</div>
			</div>
		</div>
</div>

<script>

function component_selected(id)
{
	console.log("loaded ",id);
	var pt = get_comp_by_id(id,'prep_type');
	console.log("loaded ",id,pt);
	document.getElementsByName('new_comp_prep_type')[0].value = pt;
	
	document.getElementById('new_comp_prep_type_code').innerHTML = get_preptype_val(pt,'code');;
}
function show_active_components(data)
{
	var div = document.getElementById('active_comps');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	div.innerHTML = "<h1>Active Components</h1>";
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	tr.appendChild(new_td('ID','comp'));
	tr.appendChild(new_td('Prep<br>Type','comp'));
    tr.appendChild(new_td('Description','comp'));   
    tr.appendChild(new_td('M1<br>Time','comp'));
    tr.appendChild(new_td('M1<br>Temp','comp'));
    tr.appendChild(new_td('M2<br>Time','comp'));
    tr.appendChild(new_td('Action','comp'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		tr.appendChild(new_td(data[i]['id'],'comp'));
   		tr.appendChild(new_td(get_preptype_val(data[i]['prep_type_id'],'code'),'comp'));
   		tr.appendChild(new_td(data[i]['description'],'comp'));
   		var M1_time = new Date(data[i]['M1_time']);
   		// var M1_t = M1_time.getHours() + ":" + M1_time.getMinutes();
   		tr.appendChild(new_td(show_time(M1_time),'comp'));
   		// tr.appendChild(new_td(data[i]['M1_time'],'comp'));
   		tr.appendChild(new_td(data[i]['M1_temp'],'comp'));
   		var M2t = data[i]['M2_time'];
   		console.log("M2 time -" + data[i]['M2_time'] + "-" + M2t.length);
   		
   		if (M2t.length > 0 ) {
   			var M2_time = new Date(data[i]['M2_time']);
   			tr.appendChild(new_td(show_time(M2_time),'comp'));
   		}
   		else {
   	   		var M2_time = M1_time;
   	   		var mins_due = parseInt(get_preptype_val(data[i]['prep_type_id'],'M2_time_minutes'));
   	   		M2_time.setMinutes(M1_time.getMinutes() + mins_due);
   	   		var now = new Date();
   	   		var timeDiff = parseInt((now.getTime() - M2_time.getTime()) / (1000 * 60));
   	   		if (timeDiff > 0) {
   	   			// tr.appendChild(new_td('due:' + show_time(M2_time) + "-" + timeDiff,'comp'));
   	   			tr.appendChild(new_td(format_minutes(timeDiff) + " OVERDUE",'comp red'));
   	   		}
   	   		else {
   	   			tr.appendChild(new_td(format_minutes(timeDiff) + " remaining",'comp'));
   	   		}
   			// tr.appendChild(new_td('due','comp'));
   		}
   		tr.appendChild(new_td("<button type='button' class='acs_comp_btn' onclick='act_component(" + i + ");'>Action</button>",'comp'));
   		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}
function goto_start_new_comp()
{
	console.log('goto_start_new_comp');
	load_comps();
	openPage('new_comp', this, 'red','comp_details','acs_comp_btn');
}
function goto_active_components()
{
	console.log('goto_active_components');
	openPage('active_comps', this, 'red','comp_details','acs_comp_btn');
	document.getElementById('active_comps').innerHTML = "loading....";
	 $.ajax({
	        url: "REST/get_active_comps.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            console.log("got " + result.length + " comps");
	            show_active_components(result);
	            
	        },
	        done: function(result) {
	            console.log("done load_comps ");
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}

var active_component_index = -1;
function act_component(i)
{
	console.log("action for active component ",i);
	if (i < 0 || i > active_comps.length) {
		console.log("invalid index ",active_comps.length);
		return;
	}
	console.log("action for active component ",i,active_comps[i]['id']);
	active_component_index = i; // global
	console.log("act_component M2 time ",active_comps[i]['M2_time']);
	
	console.log("action for active component ",active_comps[i]['description']);
	if (active_comps[i]['M2_time'].length < 5) {
		document.getElementById('comp_M2_correct').innerHTML = "";
		document.getElementById('comp_action_modal_M2').style.display = 'block';
		document.getElementById('comp_modal_title_div').innerHTML = active_comps[i]['description'];
		var body = document.getElementById('comp_action_body_div');
		
		document.getElementById('M2_target_temp').innerHTML = "< " + get_preptype_val(active_comps[i]['prep_type_id'],'M2_temp');
	}
	else { // M3
		document.getElementById('comp_M3_correct').innerHTML = "";
		document.getElementById('comp_action_modal_M3').style.display = 'block';
		document.getElementById('comp_modal_title_div').innerHTML = active_comps[i]['description'];
		var body = document.getElementById('comp_action_body_div');
		
		document.getElementById('M3_target_temp').innerHTML = "< " + get_preptype_val(active_comps[i]['prep_type_id'],'M3_temp');
	}
}

function comp_M2()
{
	var M2_temp_reading = parseInt(document.getElementsByName('M2_temp')[0].value);
	var M2_temp_limit = parseInt(get_preptype_val(active_comps[active_component_index]['prep_type_id'],'M2_temp'));
	
	if (M2_temp_reading > M2_temp_limit) {
		console.log("M2 temp " + M2_temp_reading + " over limit " + M2_temp_limit);
		document.getElementById('comp_M2_correct').innerHTML = "M2 temp " + M2_temp_reading + " over limit " + M2_temp_limit;
		document.getElementById('comp_M2_correct').innerHTML += "<br>Take corrective action";
	}
	else {
		// send data to REST interface
		document.getElementById('comp_action_modal_M2').style.display = 'none';
		var component = new Object();
		component.id = active_comps[active_component_index]['id'];
		component.M2_temp = document.getElementsByName('M2_temp')[0].value;
		component.M2_chef_id = document.getElementsByName('M2_chef_id')[0].value;
		var data =  {data: JSON.stringify(component)};
	    console.log("Sent Off: %j", data);
	    
	    $.ajax({
	        url: 'REST/M2_comp.php',
	        type: "POST",
	        data: data,

	        success: function(result) {
	            console.log("start_component result ",result);
	            goto_active_components();
	        },
	        done: function(result) {
	            console.log("done start_component result ",result);
	        },
	        fail: (function (result) {
	            console.log("start_componentfail ",result);
	        })
	    });
	    
	}
	
}
function delete_comp()
{
	var component = new Object();
	component.id = active_comps[active_component_index]['id'];

	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: 'REST/delete_comp.php',
        type: "POST",
        data: data,

        success: function(result) {
            console.log("delete_component result ",result);
            goto_active_components();
        },
        
        fail: (function (result) {
            console.log("delete_component fail ",result);
        })
    });
    document.getElementById('comp_action_modal_M2').style.display = 'none';
    document.getElementById('comp_action_modal_M3').style.display = 'none';
}

function comp_M3()
{
	var M3_temp_reading = parseInt(document.getElementsByName('M3_temp')[0].value);
	var M3_temp_limit = parseInt(get_preptype_val(active_comps[active_component_index]['prep_type_id'],'M3_temp'));
	
	if (M3_temp_reading > M3_temp_limit) {
		console.log("M3 temp " + M3_temp_reading + " over limit " + M3_temp_limit);
		document.getElementById('comp_M3_correct').innerHTML = "M3 temp " + M3_temp_reading + " over limit " + M3_temp_limit;
		document.getElementById('comp_M3_correct').innerHTML += "<br>Take corrective action";
	}
	else {
		// send data to REST interface
		document.getElementById('comp_action_modal_M3').style.display = 'none';
		var component = new Object();
		component.id = active_comps[active_component_index]['id'];
		component.M3_temp = document.getElementsByName('M3_temp')[0].value;
		component.M3_chef_id = document.getElementsByName('M3_chef_id')[0].value;
		var data =  {data: JSON.stringify(component)};
	    console.log("Sent Off: %j", data);
	    
	    $.ajax({
	        url: 'REST/M3_comp.php',
	        type: "POST",
	        data: data,

	        success: function(result) {
	            console.log("start_component result ",result);
	            goto_active_components();
	        },
	        done: function(result) {
	            console.log("done start_component result ",result);
	        },
	        fail: (function (result) {
	            console.log("start_componentfail ",result);
	        })
	    });
	    
	}
	
}
function start_component()
{
	var component = new Object();
	component.description = document.getElementsByName('new_comp_desc')[0].value;
	component.prep_type = document.getElementsByName('new_comp_prep_type')[0].value;
	component.M1_temp = document.getElementsByName('M1_temp')[0].value;
	component.M1_chef_id = document.getElementsByName('M1_chef_id')[0].value;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    document.getElementsByName('new_comp_desc')[0].value = '';
    document.getElementsByName('M1_temp')[0].value = '';
    $.ajax({
        url: 'REST/new_comp.php',
        type: "POST",
        data: data,

        success: function(result) {
            console.log("start_component result ",result);
            goto_active_components();
        },
        done: function(result) {
            console.log("done start_component result ",result);
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
    
}
</script>