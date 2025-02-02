<script>

var report_mode = null; // can be dock, kitchen or plating
var kitchen_report_fmt = {
	'CC': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'Use By':'expiry_date',
		'CHEF':'M1_chef',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp',
		'Q/A':'M1_action_code',
		'QA':'M1_action_id'},
	'HF': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'Use By':'expiry_date',
		'CHEF':'M1_chef',
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
		'Use By':'expiry_date',
		'CHEF':'M1_chef',
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
		'TIME':'M1_time',
		},
	'AHR': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'Use By':'expiry_date',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'Q/A':'M1_action_code',
		'CHEF':'M1_action_id'
	},
	'DECANT': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'CHEF':'M1_check',
		'EXPIRY DATE':'expiry_date'
	}
}
var dock_report_fmt = {
	'FRESH': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Use By':'expiry_date',
		'Q/A':'M1_action_text',
		'RECEIVED BY':'M1_check'},
	'FROZEN': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Use By':'expiry_date',
		'Q/A':'M1_action_text',
		'RECEIVED BY':'M1_check'},
	'DRY': {
		'COMPONENT NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'Use By':'expiry_date',
		'Q/A':'M1_action_text',
		'RECEIVED BY':'M1_check'}
}

var plating_report_fmt = {
		'ITEM CODE':'code',
		'ITEM NAME':'menu_item',
		'TIME':'time_started',
		'ITEM COMPONENTS':'description',
		'BATCH CODE':'component_id',
		'QTY':'qty',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'CORRECTIVE ACTION':'ca'
		
}
var plating_item_report_fmt = {
		'ITEM COMPONENTS':'description',
		'BATCH CODE':'component_id',
		'QTY':'qty',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'CORRECTIVE ACTION':'ca'
		
}

function reports()
{

	load_menu_items();
	// load_qa();
	openPage('REPORTS', this, 'red','tabcontent','tabclass');
	load_plating_data();
}
function report_components(data,format,target)
{
	
	console.log('report_components',data.length);
	console.log(data);
	// var div = document.getElementById('report_container');
	var div = document.getElementById(target);
	div.innerHTML = '';
	if (data.length < 1) {
		div.innerHTML = "<span class='reports_message' >No Active Components</span>";
		return;
	}
	var tab = document.createElement('table');
	tab.className = 'component_table';
    tab.id = 'reports';
	for (var preptype_idx = 0; preptype_idx < preptypes.length; preptype_idx++) {
		console.log(preptypes[preptype_idx]['code']);
		
		var preptype = preptypes[preptype_idx]['code'];
		var preptype_id = preptypes[preptype_idx]['id'];
		console.log(preptypes[preptype_idx]['code'],preptype_id);
		if (format[preptype]) {
			//var preptype_id = preptypes[preptype_idx]['id'];
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
		  // 	for (var i=0; i<data.length; i++) {
		  	for (var i = data.length - 1; i >= 0; i--) {
		/*	   	console.log('item ' + data[i]['description'] + ' prep ' + data[i]['prep_type_id'] + 
					  " : " + preptype_id); */
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
			   			else {
				   			
			   				td.innerHTML = report_fmt_str(e,data[i][e]);
			   				if (j === 'COMPONENT NAME') {
			   					if (data[i]['subs']) {
			   						tr.setAttribute("onclick","kitchen_details(" + data[i]['id'] + ");");
					   				td.style.color = 'green';
					   				td.innerHTML += " (";
					   				for (var jj = 0; jj < data[i]['subs'].length; jj++) {
						   				if (jj > 0) td.innerHTML += ", ";
					   					td.innerHTML += data[i]['subs'][jj]['description'];
					   				}
					   				td.innerHTML += ")";
				   				}
				   				/*
				   				if (data[i]['ingredients']) {
					   				td.style.color = 'green';
					   				td.innerHTML += " (";
					   				td.innerHTML += data[i]['ingredients'];
					   				
					   				td.innerHTML += ")";
				   				}
				   				*/
				   			}
				   			
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

function get_active_comp_by_id(cid)
{
	for (var i = 0; i < active_comps.length; i++) {
		if (active_comps[i].id == cid) {
			return (active_comps[i]);
		}
	}
}

function kitchen_details(id,rownum)
{
	console.log("show details for component " + id);
	var comp = get_active_comp_by_id(id);
	show ('ingredients_div');
	if (comp['subs'] && comp['subs'].length > 0) {
		console.log(comp);
		report_components(comp['subs'],dock_report_fmt,'ingredients_table');	 
	}
}

function kitchen_reports(format,tab,mode)
{
	
	var search_terms = new Object();
	search_terms.start_date = document.getElementById('report_start').value;
	search_terms.end_date = document.getElementById('report_end').value;
	search_terms.search_for = document.getElementById('report_search').value;
	search_terms.all = true;
	var data =  {data: JSON.stringify(search_terms)};
	report_mode = mode;
	load_preptypes();
	// really lazy .... must fix
	document.getElementById('dock_report_tab').className = 'top_menu';
	document.getElementById('kitchen_report_tab').className = 'top_menu';
	document.getElementById('plating_report_tab').className = 'top_menu';
	document.getElementById(tab).className = 'top_menu_highlighted';
	var div = document.getElementById('report_container');
	console.log(data);
	div.innerHTML = '';
	 $.ajax({
	        url:  "REST/get_active_comps.php",
	        type: "POST",
	        dataType: 'json',
	        data: data,	      
	        success: function(result) {
		        console.log(result);
	            active_comps = result;	          
	            console.log("REPORTS got " + active_comps.length + " comps");
	            report_components(active_comps,format,'report_container');	            
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}


function load_plating_data()
{
	report_mode = 'plating'
	// load_plating_items(plating_reports);
	console.log('load_plating_data');
	var data = get_search_terms();
	console.log(data);
	$.ajax({
    	url: RESTHOME + "get_plating_report.php",
        type: "POST",
        dataType: 'json',
        data: data,
        success: function(result) {
            plating_items = result; // need to populate with descritions
            console.log('load_plating_items got result');
            console.log(result);
            if (!result || result.error ) {
            	console.log('forcing reload');
            	location.reload(true);
            }
            for (var i = 0; i < plating_items.length; i++) {
            	
            	var menu_item = get_menu_item_by_id(plating_items[i].menu_item_id);
            	plating_items[i].dish_name = menu_item.dish_name;
            	plating_items[i].code = menu_item.code;
            	console.log('loading plating item ' + menu_item.dish_name);
            	for (var j = 0; j < plating_items[i].items.length; j++) {
            		console.log('loading plating item ' + j, plating_items[i].items[j].menu_item_component_id);
            		// var comp = get_component_by_id(plating_items[i].items[j].menu_item_component_id);
            		let comp = plating_items[i].items[j]['comp'];
            		plating_items[i].items[j].description = comp.description;
            	}
            }
            plating_reports();
            console.log("got " + result.length + " plating items");           
        },
        fail: (function (result) {
            console.log("fail load_plating_items",result);
        })
    });
}

function report_fmt_str(field,value)
{
	if (value) {
		if (field.indexOf('time') >= 0 || field.indexOf('date') >= 0) {
	   	
   			if (value.length > 0) {
   				return(value.substring(11,16) + " " + value.substring(8,10) + '/' + value.substring(5,7)) ;
   			}
		}
		else 
			return(value);
	}

	return('-');
}

function plating_comp_details(plating_item_id,item_id,comp_id)
{
	console.log('plating_comp_details',plating_item_id,item_id,comp_id);
	document.getElementById('component_detail_table').innerHTML = '';
	console.log(plating_items);
	show ('component_div');
	let comp = plating_items[plating_item_id].items[item_id].comp;
	if (comp) {
		console.log(comp);
		// document.getElementById('component_detail_table').innerHTML = JSON.stringify(comp);
		var tab = document.createElement('table');
		var tr2 = document.createElement('tr');
		let preptype = comp.prep_type;
		// headings
		for (var i in kitchen_report_fmt[preptype]) {
	//	for (var i in format[comp.preptype]) {
			var th = document.createElement('th');
			th.innerHTML = margin(i);
			tr2.appendChild(th);   
			
		}
		tab.appendChild(tr2);
		var tr2 = document.createElement('tr');
		for (var j in kitchen_report_fmt[preptype]) {
	   		
   			var td = document.createElement('td');
   			// var e = kitchen_report_fmt[preptype][j];
   			var e = kitchen_report_fmt[preptype][j];
   			if (j === 'BATCH CODE') {
   				// td.innerHTML = 'c01' + zeropad(data[i][e],6);
   				
   				td.innerHTML = sprintf('C01%06d',comp[e]);
   				
   				
   			}
   			else {
	   			
   				td.innerHTML = report_fmt_str(e,comp[e]);
   			}
   			tr2.appendChild(td); 
		}
		tab.appendChild(tr2);
		document.getElementById('component_detail_table').appendChild(tab);
		// report_components(comp,dock_report_fmt,'ingredients_table');	 
	}
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
	tab.id = 'reports';
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
        	// TODO - better report system
    		var tr = document.createElement('tr');
    		
    		var td = document.createElement('td');
       		td.innerHTML = (j == 0) ?plating_items[i].code :'';
       		tr.appendChild(td);  
       		var td = document.createElement('td');
       		td.innerHTML = (j == 0) ?plating_items[i].dish_name:'';
       		tr.appendChild(td);    
       		var td = document.createElement('td');
       		td.innerHTML = (j == 0) ?report_fmt_str('time_started',plating_items[i].time_started):'';
       		tr.appendChild(td);    
    		for (var ii in plating_item_report_fmt) {
    			var td = document.createElement('td');
    	   		var field = plating_item_report_fmt[ii];
    	   		var value = plating_items[i].items[j][plating_item_report_fmt[ii]];
    	   		td.innerHTML = report_fmt_str(field,value); // + field + ":" + value;
    	   		td.setAttribute("onclick","plating_comp_details(" + i + "," + j + "," +  plating_items[i].items[j]['component_id'] + ");");
    	   		// td.setAttribute("onclick","kitchen_details(" + data[i]['id'] + ");");
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

function search_report()
{
	if (report_mode == 'kitchen') {
		kitchen_reports(kitchen_report_fmt,'kitchen_report_tab','kitchen');
	}
	else if (report_mode == 'dock') {
		kitchen_reports(dock_report_fmt,'dock_report_tab','dock');
	}
	else if (report_mode == 'plating') {
		load_plating_items(plating_reports);
	}
	else {
		console.log('unknown report mode' + report_mode);
	}
}

</script>
<div class='top_menu_container'>
			<div class='top_menu' id='dock_report_tab'  onclick="kitchen_reports(dock_report_fmt,'dock_report_tab','dock')">DOCK</div>
			<div class='top_menu' id='kitchen_report_tab'  onclick="kitchen_reports(kitchen_report_fmt,'kitchen_report_tab','kitchen')">KITCHEN</div>
			<div class='top_menu' id='plating_report_tab'  onclick="load_plating_data();">PLATING</div>
			<div class='top_menu' id='report_range_tab'>
				<input type="text" id="report_start" name="report_start" placeholder='Start date' class='datepicker' readonly="readonly"></td>
				<input type="text" id="report_end" name="report_end" placeholder='End date' class='datepicker' readonly="readonly">
				<input type="text" id="report_search" name="report_search" placeholder="Search" onchange='search_report();'>
				<button class='button_main' id='go' onclick='search_report();'>Go</button>
			</div>
			
</div>
<div class='acs_main' id="reports_frame">

<div class='popup' id='ingredients_div'>

	<h1>Ingredients</h1>
	<div id='ingredients_table'></div>
	<button class='button' onclick='hide("ingredients_div");'>Close</button>
</div>
<div class='popup' id='component_div'>

	<h1>Component details</h1>
	<div id='component_detail_table'></div>
	<button class='button' onclick='hide("component_div");'>Close</button>
</div>
<div id='report_container' class='acs_container'>
    <span class="reports_message">Select a date range and click 'Go' </span>
</div>
</div>
