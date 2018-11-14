<script>

var kitchen_report_fmt = {
	'CC': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'},
	'HF': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'},
	'ESL': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'},

	'LR': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		},
	'AHR': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'
	}
}
var dock_report_fmt = {
	'FRESH': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'},
	'FROZEN': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'},
	'DRY': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'}
}

var plating_report_fmt = {
		'ITEM CODE':'code',
		'ITEM NAME':'menu_item',
		'ITEM COMPONENTS':'description',
		'BATCH CODE':'id',
		'QTY':'qty',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'CORRECTIVE ACTION':'ca'
		
}
var plating_item_report_fmt = {
		'ITEM COMPONENTS':'description',
		'BATCH CODE':'id',
		'QTY':'qty',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'CORRECTIVE ACTION':'ca'
		
}

function reports()
{
	load_chefs();
	openPage('REPORTS', this, 'red','tabcontent','tabclass');
}
function report_components(data,format)
{
	// console.log(data);
	var div = document.getElementById('report_container');
	
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	var tab = document.createElement('table');
	tab.className = 'component_table';
	for (var preptype_idx = 0; preptype_idx < preptypes.length; preptype_idx++) {
		console.log(preptypes[preptype_idx]['code']);
		
		var preptype = preptypes[preptype_idx]['code'];
		if (format[preptype]) {
			var preptype_id = preptypes[preptype_idx]['id'];
			var tr1 = document.createElement('tr');
			var th = document.createElement('th');
			th.rowspan = 5;
			th.innerHTML = margin(preptypes[preptype_idx]['code']);
			tr1.appendChild(th);   
		//	tab.appendChild(tr);
			var tr2 = document.createElement('tr');
			// headings
		//	for (var i in kitchen_report_fmt[preptype]) {
			for (var i in format[preptype]) {
				var th = document.createElement('th');
				th.innerHTML = margin(i);
				tr2.appendChild(th);   
				
			}
		//	tab.appendChild(tr);
			var data_count = new Object();
		   	for (var i=0; i<data.length; i++) {
			   	console.log('item ' + data[i]['description'] + ' prep ' + data[i]['prep_type_id'] + 
					  " : " + preptype_id);
			   	if (data[i]['prep_type_id'] == preptype_id) {
				   	if (!data_count[preptype_id]) {
				   		tab.appendChild(tr1);
				   		tab.appendChild(tr2);
				   		data_count[preptype_id] = 1;
				   	}
				   	var chef = get_chef_by_id(data[i]['M1_chef_id']);
				   	if (chef) data[i]['chef'] = chef['label'];
			   		var tr = document.createElement('tr');
			   		// for (var j in kitchen_report_fmt[preptype]) {
			   		for (var j in format[preptype]) {
				   		
			   			var td = document.createElement('td');
			   			// var e = kitchen_report_fmt[preptype][j];
			   			var e = format[preptype][j];
			   			if (j === 'BATCH CODE') {
			   				// td.innerHTML = 'c01' + zeropad(data[i][e],6);
			   				td.innerHTML = sprintf('C01%06d',data[i][e]);
			   			}
			   			else if (e.indexOf('time') > 0) {
			   	   			var s= data[i][e];
			   	   			td.innerHTML = s.substring(11,16);
			   			}
			   			else {
			   				td.innerHTML = data[i][e]?data[i][e]:'-';
			   			}
			   			tr.appendChild(td);   
			   		}
			   		tab.appendChild(tr);
			   	}
		   	}
		}
	}
   	div.appendChild(tab);
}

function kitchen_reports(format,tab)
{
	load_preptypes();
	// really lazy .... must fix
	document.getElementById('dock_report_tab').className = 'top_menu';
	document.getElementById('kitchen_report_tab').className = 'top_menu';
	document.getElementById('plating_report_tab').className = 'top_menu';
	document.getElementById(tab).className = 'top_menu_highlighted';
	var div = document.getElementById('report_container');
	div.innerHTML = '';
	 $.ajax({
	        url:  "REST/get_active_comps.php?all=true",
	        type: "POST",
	        dataType: 'json',	      
	        success: function(result) {
	            active_comps = result;	          
	            console.log("got " + result.length + " comps");
	            report_components(result,format);	            
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}


function load_plating_data()
{
	load_plating_items(plating_reports);
}


function plating_reports()
{
	var div = document.getElementById('report_container');
	// really lazy .... must fix
	console.log('plating_reports');
	document.getElementById('kitchen_report_tab').className = 'top_menu'
	document.getElementById('plating_report_tab').className = 'top_menu_highlighted';
	div.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	// headings
	for (var i in plating_report_fmt) {
		var th = document.createElement('th');
		th.innerHTML = margin(i);
		tr.appendChild(th);   
		
	}
	
	tab.appendChild(tr);
	for (var i = 0; i < plating_items.length; i++) {
   
    	for (var j = 0; j < plating_items[i].items.length; j++) {
    		var tr = document.createElement('tr');
    		
    		var td = document.createElement('td');
       		td.innerHTML = (j == 0) ?plating_items[i].code :'';
       		tr.appendChild(td);  
       		var td = document.createElement('td');
       		td.innerHTML = (j == 0) ?plating_items[i].dish_name:'';
       		tr.appendChild(td);    
    		for (var ii in plating_item_report_fmt) {
    			var td = document.createElement('td');
    	   		var e = plating_item_report_fmt[ii];
    	   		var s= plating_items[i].items[j][plating_item_report_fmt[ii]];
    	   		if (s) {
    	   			td.innerHTML = (e.indexOf('time') > 0)?s.substring(11,16):s;
    	   		}
    	   		
    	   		tr.appendChild(td);    
    	   		tab.appendChild(tr);
    		}
    		tab.appendChild(tr);
    		console.log('loading plating item ' + j, plating_items[i].items[j].menu_item_component_id);
    //		var comp = get_component_by_id(plating_items[i].items[j].menu_item_component_id);
    //		plating_items[i].items[j].description = comp.description;
    	}
    }
	div.appendChild(tab);
}

</script>
<div class='top_menu_container'>
			<div class='top_menu' id='dock_report_tab'  onclick="kitchen_reports(dock_report_fmt,'dock_report_tab')">DOCK</div>
			<div class='top_menu' id='kitchen_report_tab'  onclick="kitchen_reports(kitchen_report_fmt,'kitchen_report_tab')">KITCHEN</div>
			<div class='top_menu' id='plating_report_tab'  onclick="load_plating_data();">PLATING</div>
			<div class='top_menu' id='report_range_tab'">
				<input type="text" id="report_start" name="report_start" placeholder='start date' class='datepicker' readonly="readonly"></td>
				<input type="text" id="report_end" name="report_end" placeholder='end date' class='datepicker' readonly="readonly">
				<input type="text" id="report_search" name="report_search" placeholder="search" onclick='search_report();'>
				<button>go</button>
			</div>
			
</div>
<div class='acs_main'>

<div id='report_container' class='acs_container'>
Select a date range and click 'go'
</div>
</div>
