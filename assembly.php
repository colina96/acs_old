<div class='acs_main'>
	
		<div class="acs_sidebar">
		  
		  <div class='acs_space' >SELECT AN ACTION</div>
		  <button type='button' class='acs_item_btn' href="#" id="new_item_btn"  
		  	onclick="openPage('new_item', this, 'red','item_details','acs_item_btn')">START NEW</button>
		  <button type='button' class='acs_item_btn' href="#" id="active_items_btn" 
		  	onclick="goto_active_item()">ACTIVE ITEM</button>
		  
		  
		</div>
	
		<div class="acs_right_content">
			<div id='new_item' class='item_details menu_details_active'><h1>Start new item</h1>
			<table width='100%'<tr>
				<td>Search for item: <td><input type="text" id="search_menu" width:'100%' size='50' name='new_item_desc'>
			</table>
			<div id='menu_item_components_div'></div>
				
			</div>
			<div id='active_items' class='item_details'>
			</div>
			<div id='expired_menus' class='item_details'>
			</div>
			<div class='acs_modal' id='item_action_modal_M2'>
				<div class='modal_header'><span><h1>M2</h1></span>
				<div class='acs_container'><div id='item_modal_title_div'></div></div>
				<div class='close_modal' onclick='close_menu_item("item_action_modal_M2");'>X</div>
				</div>
			
				<div id='item_action_body_div'>
					<table width='100%'>
					<tr><td>Temperature : <td><input type="text" name="M2_temp" size='3'>
					<td><div id='M2_target_temp'></div></td>
					<tr><td colspan=2>Chef: <td><?php select_chef('M2_chef_id') ?>
					<tr>
				</table>
				<div id='item_M2_correct' class='acs_container'></div>
				<button type='button' class='acs_item_btn' href="#" id="item_M2_action"  
		  				onclick="item_M2()">SUBMIT</button>
				</div>
			</div>
			<div class='acs_modal' id='item_action_modal_M3'>
				<div class='modal_header'><span><h1>M3</h1></span>
				<div class='acs_container'><div id='item_modal_M3_title_div'></div></div>
				<div class='close_modal' onclick='close_menu_item("item_action_modal_M3");'>X</div>
				</div>
			
				<div id='item_action_body_M3_div'>
					<table width='100%'>
					<tr><td>Temperature : <td><input type="text" name="M3_temp" size='3'>
					<td><div id='M3_target_temp'></div></td>
					<tr><td colspan=2>Chef: <td><?php select_chef('M3_chef_id') ?>
					<tr>
				</table>
				<div id='item_M3_correct' class='acs_container'></div>
				<button type='button' class='acs_item_btn' href="#" id="item_M3_action"  
		  				onclick="item_M3()">SUBMIT</button>
				</div>
			</div>
		</div>
</div>

<script>

function get_menu_item_by_id(menu_item_id) {
	// menu_items not a hashed array because the search fn needs it that way
	for (var i = 0; i < menu_items.length; i++) {
		if (menu_items[i].id == menu_item_id) return(menu_items[i]);
	}
	return(null);
}

function show_menu_item_components(menu_item_id) {
    var div = document.getElementById('menu_item_components_div');
	clearChildren(div);
	var tab = document.createElement('table');
	tab.className = 'item_table';
	var line = 1;
	menu_item = get_menu_item_by_id(menu_item_id);
	if (menu_item != null) {
		console.log("found menu_item ",menu_item.dish_name,menu_item.items.length);
		var items = menu_item.items;
		
		for (var i = 0; i < items.length; i++) {
			
				console.log("found ",items[i].description);
				var tr = document.createElement('tr');
				tr.appendChild(new_td(line++,'item'));
				tr.appendChild(new_td(items[i].description,'item'));
				tab.appendChild(tr);
			
		}
		div.appendChild(tab);
	}
}

function show_active_item(data)
{
	var div = document.getElementById('active_items');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Items</h1>";
		return;
	}
	div.innerHTML = "<h1>Active Items</h1>";
	var tab = document.createElement('table');
	tab.className = 'item_table';
	var tr = document.createElement('tr');
	tr.appendChild(new_td('ID','item'));
	tr.appendChild(new_td('Prep<br>Type','item'));
    tr.appendChild(new_td('Description','item'));   
    tr.appendChild(new_td('M1<br>Time','item'));
    tr.appendChild(new_td('M1<br>Temp','item'));
    tr.appendChild(new_td('M2<br>Time','item'));
    tr.appendChild(new_td('Action','item'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		tr.appendChild(new_td(data[i]['id'],'item'));
   		tr.appendChild(new_td(get_preptype_val(data[i]['prep_type_id'],'code'),'item'));
   		tr.appendChild(new_td(data[i]['description'],'item'));
   		var M1_time = new Date(data[i]['M1_time']);
   		// var M1_t = M1_time.getHours() + ":" + M1_time.getMinutes();
   		tr.appendChild(new_td(show_time(M1_time),'item'));
   		// tr.appendChild(new_td(data[i]['M1_time'],'item'));
   		tr.appendChild(new_td(data[i]['M1_temp'],'item'));
   		var M2t = data[i]['M2_time'];
   		console.log("M2 time -" + data[i]['M2_time'] + "-" + M2t.length);
   		
   		if (M2t.length > 0 ) {
   			var M2_time = new Date(data[i]['M2_time']);
   			tr.appendChild(new_td(show_time(M2_time),'item'));
   		}
   		else {
   	   		var M2_time = M1_time;
   	   		var mins_due = parseInt(get_preptype_val(data[i]['prep_type_id'],'M2_time_minutes'));
   	   		M2_time.setMinutes(M1_time.getMinutes() + mins_due);
   	   		var now = new Date();
   	   		var timeDiff = parseInt((now.getTime() - M2_time.getTime()) / (1000 * 60));
   	   		if (timeDiff > 0) {
   	   			// tr.appendChild(new_td('due:' + show_time(M2_time) + "-" + timeDiff,'item'));
   	   			tr.appendChild(new_td('overdue:' + timeDiff,'item'));
   	   		}
   	   		else {
   	   			tr.appendChild(new_td('due:' + timeDiff,'item'));
   	   		}
   			// tr.appendChild(new_td('due','item'));
   		}
   		tr.appendChild(new_td("<button type='button' class='acs_item_btn' onclick='act_item(" + i + ");'>Action</button>",'item'));
   		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}
function goto_active_item()
{
	console.log('goto_active_item');
	openPage('active_items', this, 'red','item_details','acs_item_btn');
	document.getElementById('active_items').innerHTML = "loading....";
	 $.ajax({
	        url: "REST/get_active_items.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_items = result;
	           // document.getElementById('active_items').innerHTML = result;
	            console.log("got " + result.length + " items");
	            show_active_item(result);
	            
	        },
	        done: function(result) {
	            console.log("done load_items ");
	        },
	        fail: (function (result) {
	            console.log("fail load_items",result);
	        })
	    });
}

var active_item= -1;
function act_item(i)
{
	console.log("action for active item",i);
	if (i < 0 || i > active_items.length) {
		console.log("invalid index ",active_items.length);
		return;
	}
	active_item= i; // global
	console.log("act_itemM2 time ",active_items[i]['M2_time']);
	
	console.log("action for active item",active_items[i]['description']);
	if (active_items[i]['M2_time'].length < 5) {
		document.getElementById('item_M2_correct').innerHTML = "";
		document.getElementById('item_action_modal_M2').style.display = 'block';
		document.getElementById('item_modal_title_div').innerHTML = active_items[i]['description'];
		var body = document.getElementById('item_action_body_div');
		
		document.getElementById('M2_target_temp').innerHTML = "< " + get_preptype_val(active_items[i]['prep_type_id'],'M2_temp');
	}
	else { // M3
		document.getElementById('item_M3_correct').innerHTML = "";
		document.getElementById('item_action_modal_M3').style.display = 'block';
		document.getElementById('item_modal_title_div').innerHTML = active_items[i]['description'];
		var body = document.getElementById('item_action_body_div');
		
		document.getElementById('M3_target_temp').innerHTML = "< " + get_preptype_val(active_items[i]['prep_type_id'],'M3_temp');
	}
}

function item_M2()
{
	var M2_temp_reading = parseInt(document.getElementsByName('M2_temp')[0].value);
	var M2_temp_limit = parseInt(get_preptype_val(active_items[active_item]['prep_type_id'],'M2_temp'));
	
	if (M2_temp_reading > M2_temp_limit) {
		console.log("M2 temp " + M2_temp_reading + " over limit " + M2_temp_limit);
		document.getElementById('item_M2_correct').innerHTML = "M2 temp " + M2_temp_reading + " over limit " + M2_temp_limit;
		document.getElementById('item_M2_correct').innerHTML += "<br>Take corrective action";
	}
	else {
		// send data to REST interface
		document.getElementById('item_action_modal_M2').style.display = 'none';
		var item= new Object();
		item.id = active_items[active_item]['id'];
		item.M2_temp = document.getElementsByName('M2_temp')[0].value;
		item.M2_chef_id = document.getElementsByName('M2_chef_id')[0].value;
		var data =  {data: JSON.stringify(item)};
	    console.log("Sent Off: %j", data);
	    
	    $.ajax({
	        url: 'REST/M2_item.php',
	        type: "POST",
	        data: data,

	        success: function(result) {
	            console.log("start_item result ",result);
	            goto_active_item();
	        },
	        done: function(result) {
	            console.log("done start_item  result ",result);
	        },
	        fail: (function (result) {
	            console.log("start_item fail ",result);
	        })
	    });
	    
	}
	
}
function item_M3()
{
	var M3_temp_reading = parseInt(document.getElementsByName('M3_temp')[0].value);
	var M3_temp_limit = parseInt(get_preptype_val(active_items[active_item_index]['prep_type_id'],'M3_temp'));
	
	if (M3_temp_reading > M3_temp_limit) {
		console.log("M3 temp " + M3_temp_reading + " over limit " + M3_temp_limit);
		document.getElementById('item_M3_correct').innerHTML = "M3 temp " + M3_temp_reading + " over limit " + M3_temp_limit;
		document.getElementById('item_M3_correct').innerHTML += "<br>Take corrective action";
	}
	else {
		// send data to REST interface
		document.getElementById('item_action_modal_M3').style.display = 'none';
		var item = new Object();
		item.id = active_items[active_item_index]['id'];
		item.M3_temp = document.getElementsByName('M3_temp')[0].value;
		item.M3_chef_id = document.getElementsByName('M3_chef_id')[0].value;
		var data =  {data: JSON.stringify(item)};
	    console.log("Sent Off: %j", data);
	    
	    $.ajax({
	        url: 'REST/M3_item.php',
	        type: "POST",
	        data: data,

	        success: function(result) {
	            console.log("start_item result ",result);
	            goto_active_items();
	        },
	        done: function(result) {
	            console.log("done start_item result ",result);
	        },
	        fail: (function (result) {
	            console.log("start_itemfail ",result);
	        })
	    });
	    
	}
	
}
function start_item()
{
	var item = new Object();
	item.description = document.getElementsByName('new_item_desc')[0].value;
	item.prep_type = document.getElementsByName('new_item_prep_type')[0].value;
	item.M1_temp = document.getElementsByName('M1_temp')[0].value;
	item.M1_chef_id = document.getElementsByName('M1_chef_id')[0].value;
	var data =  {data: JSON.stringify(item)};
    console.log("Sent Off: %j", data);
    document.getElementsByName('new_item_desc')[0].value = '';
    document.getElementsByName('M1_temp')[0].value = '';
    $.ajax({
        url: 'REST/new_item.php',
        type: "POST",
        data: data,

        success: function(result) {
            console.log("start_item result ",result);
            goto_active_items();
        },
        done: function(result) {
            console.log("done start_item result ",result);
        },
        fail: (function (result) {
            console.log("start_item fail ",result);
        })
    });
    
}
</script>
