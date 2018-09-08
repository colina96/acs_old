<script>

var kitchen_report_fmt = {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp'
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
function report_components(data)
{
	var div = document.getElementById('report_container');
	
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	var tab = document.createElement('table');
	tab.className = 'component_table';
	for (var preptype_idx = 0; preptype_idx < preptypes.length; preptype_idx++) {
		console.log(preptypes[preptype_idx]['code']);
		var tr = document.createElement('tr');
		var th = document.createElement('th');
		th.rowspan = 5;
		th.innerHTML = preptypes[preptype_idx]['code'];
		tr.appendChild(th);   
		tab.appendChild(tr);
		var tr = document.createElement('tr');
		// headings
		for (var i in kitchen_report_fmt) {
			var th = document.createElement('th');
			th.innerHTML = i;
			tr.appendChild(th);   
			
		}
		tab.appendChild(tr);
	   	for (var i=0; i<data.length; i++) {
		//   	console.log('item ' + data[i]['description'] + ' prep ' + data[i]['prep_type_id']);
		   	if (data[i]['prep_type_id'] == preptypes[preptype_idx]['id']) {
		   		var tr = document.createElement('tr');
		   		for (var j in kitchen_report_fmt) {
		   			var td = document.createElement('td');
		   			var e = kitchen_report_fmt[j];
		   			if (e.indexOf('time') > 0) {
		   	   			var s= data[i][e];
		   	   			td.innerHTML = s.substring(11,16);
		   			}
		   			else {
		   				td.innerHTML = data[i][e];
		   			}
		   			tr.appendChild(td);   
		   		}
		   		tab.appendChild(tr);
		   	}
	   	}
	}
   	div.appendChild(tab);
}

function kitchen_reports(t)
{
	load_preptypes();
	if (t) t.color = 'red';// test
	var div = document.getElementById('report_container');
	div.innerHTML = '';
	 $.ajax({
	        url:  "REST/get_active_comps.php?all=true",
	        type: "POST",
	        dataType: 'json',	      
	        success: function(result) {
	            active_comps = result;	          
	            console.log("got " + result.length + " comps");
	            report_components(result,true);	            
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
	div.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	// headings
	for (var i in plating_report_fmt) {
		var th = document.createElement('th');
		th.innerHTML = i;
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
			<div class='top_menu' id='kitchen_report_tab'  onclick="kitchen_reports(this)">KITCHEN</div>
			<div class='top_menu' id='plating_report_tab'  onclick="load_plating_data();">PLATING</div>
			<div class='top_menu' id='report_range_tab'">
				<input type="text" id="report_start" name="report_start" placeholder='start date' class='datepicker' readonly="readonly"></td>
				<input type="text" id="report_end" name="report_end" placeholder='end date' class='datepicker' readonly="readonly">
				<button>go</button>
			</div>
			
</div>
<div class='acs_main'>

<div id='report_container' class='acs_container'>
Select a date range and click 'go'
</div>
</div>
