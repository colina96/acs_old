<script>

var kitchen_report_fmt = {
		'COMPONENT_NAME':'description',
		'BATCH CODE':'id',
		'M1 TIME':'M1_time',
		'M1 TEMP':'M1_temp',
		'M2 TIME':'M2_time',
		'M2 TEMP':'M2_temp',
		'M3 TIME':'M3_time',
		'M3 TEMP':'M3_temp'
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
function plating_reports()
{
	
}
</script>
<div class='top_menu_container'>
			<div class='top_menu' id='kitchen_report_tab'  onclick="kitchen_reports(this)">KITCHEN</div>
			<div class='top_menu' id='plating_report_tab'  onclick="plating_reports();">PLATING</div>
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
