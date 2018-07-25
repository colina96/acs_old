<div class='acs_main'>
	
		<div class="acs_sidebar">
		  
		  <div class='acs_menu_space' >SELECT AN ACTION</div>
		  <button type='button' class='acs_comp_btn' href="#" id="new_comp_btn"  
		  	onclick="openPage('new_comp', this, 'red','comp_details','acs_comp_btn')">START NEW</button>
		  <button type='button' class='acs_comp_btn' href="#" id="active_comps_btn" 
		  	onclick="goto_active_components()">ACTIVE COMPONENTS</button>
		  
		  
		</div>
	
		<div class="acs_right_content">
			<div id='new_comp' class='comp_details menu_details_active'><h1>Start new component</h1>
			<table width='100%'<tr>
				<td>Search for component: <td><input type="text" id="search" width:'100%' size='50' name='new_comp_desc'>
				<tr><td>Prep type<td> 				
				<?php select_prep_type('new_comp_prep_type',-1) ?>
				<tr>
				<td>Temperature : <td><input type="text" name="M1_temp" >
				<tr><td>Chef: <td><?php select_chef('M1_chef_id') ?>
				<tr>
				<td>Number of labels : <td><input type="text" name="M1_labels" >
				</table>
				<button type='button' class='acs_comp_btn' href="#" id="new_comp_select"  
		  	onclick="start_component()">START NEW</button>
			</div>
			<div id='active_comps' class='comp_details'>
			</div>
			<div id='expired_menus' class='comp_details'>
			</div>

		</div>
</div>
<script>
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
function show_active_components(data)
{
	var div = document.getElementById('active_comps');
	div.innerHTML = "<h1>Active Components</h1>";
	var tab = document.createElement('table');
	var tr = document.createElement('tr');
	tr.appendChild(new_td('ID','comp'));
	tr.appendChild(new_td('Prep<br>Type','comp'));
    tr.appendChild(new_td('Description','comp'));   
    tr.appendChild(new_td('M1<br>Time','comp'));
    tr.appendChild(new_td('M1<br>Temp','comp'));
    tr.appendChild(new_td('M2<br>Time','comp'));
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
   	   		tr.appendChild(new_td('due:' + show_time(M2_time),'comp'));
   			// tr.appendChild(new_td('due','comp'));
   		}
   		tab.appendChild(tr);
    }
   	div.appendChild(tab);
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
function start_component()
{
	var component = new Object();
	component.description = document.getElementsByName('new_comp_desc')[0].value;
	component.prep_type = document.getElementsByName('new_comp_prep_type')[0].value;
	component.M1_temp = document.getElementsByName('M1_temp')[0].value;
	component.M1_chef_id = document.getElementsByName('M1_chef_id')[0].value;
	var data =  {data: JSON.stringify(component)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: 'REST/new_comp.php',
        type: "POST",
        data: data,

        success: function(result) {
            console.log("start_component result ",result);
        },
        done: function(result) {
            console.log("done start_component result ",result);
        },
        fail: (function (result) {
            console.log("start_componentfail ",result);
        })
    });
    goto_active_components();
}
</script>