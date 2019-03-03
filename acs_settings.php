<script>

function set_probe(code,value)
{
	var fld = 'probe_type'
	$.post("REST/update_pt.php",
		    {
		        code: code,
		        fld: fld,
		        value: value
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		        load_show_preptypes();
		    });
}
function set_pt_val(inp,code,fld)
{
	console.log(inp.value,code,fld);
	var value = inp.value;
	$.post("REST/update_pt.php",
		    {
		        code: code,
		        fld: fld,
		        value: value
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		        load_show_preptypes();
		    });
}

var preptypes = null;
function load_show_preptypes()
{
	openPage('SETTINGS', this, 'red','tabcontent','tabclass');
console.log("loading prep types");
    $.ajax({
        url: RESTHOME + "get_preptypes.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
        	preptypes = result;
            console.log(preptypes);
            console.log(preptypes[0]);
            
            show_preptypes();
       },
       fail: (function (result) {
            console.log("fail preptypes",result);
        })
    });
}

function show_preptypes()
{
	
	var rows = ["PREP TYPE","Q-Pack DATA<br>RANGE OFFSET",
	        	"MILESTONE 1<br>(TEMPERATURE)" ,
			"MILESTONE 2<br>(TIME)" ,
			"MILESTONE 2<br>(TEMPERATURE)",
			"MILESTONE 3<br>(TIME)",
			"MILESTONE 3<br>(TEMPERATURE)",
			"SHELF LIFE",
			"SENSOR",
			"ALARM TIME M2",
			"ALARM TIME M3",
	];
	var flds = [
			"code",
			 "days_offset",
			"M1_temp",
			"M2_time_minutes",
			"M2_temp",
			"M3_time_minutes",
			"M3_temp",
			"shelf_life_days",
			"probe_type",
			"M2_alarm_min",
			"M3_alarm_min",
	];
	var html = "<div class='m-10'>";
	html += "<table border=0 width='100%' class='table' id='settings'>";
	for (var row = 0; row < rows.length; row++) {
		html += "<tr><th>" + rows[row] + "</th>";
		for (var col = 0; col < preptypes.length; col++) {
			var pp = preptypes[col];
			var val = pp[flds[row]];
			// console.log(pp);
			if (rows[row].indexOf("TIME") > 0) {
				var min = parseInt(val);
				var content = '';
				
				if (!min || min < 1) {
					html += "<td>-</td>";
				}
				else if (min < 60) {
					html += "<td onclick=\"set_pt_time(" + min + ",'" + pp['code'] + "','" + flds[row] + "');\">";
					html +=  val + " minutes</td>";
				}
				else {
					var hrs = min / 60;
					html += "<td onclick=\"set_pt_time(" + min + ",'" + pp['code'] + "','" + flds[row] +"','" + rows[row] + "');\">";
					if (hrs > 1) {
						html +=  hrs + " hours</td>";
					}
					else {
						html +=  hrs + " hour</td>";
					}
				}
			}
			else if (rows[row].indexOf('SENSOR') >= 0) {
				if (val == 0) {
					html += "<td onclick=\"set_probe('" + pp['code'] + "',1);\">" + 'IR' + "</td>";
				}
				else {
					html += "<td onclick=\"set_probe('" + pp['code'] + "',0);\">" + 'PROBE' + "</td>";
				}
			}
			else {
				if (row == 0) {
					html += "<td>" + val + "</td>";
				}
				else {
					if (!val) {
						val = '';
					}
				
				//	html += "<td>" + val + "</td>";
					html += "<td><input type='number' class='pt_edit' value='" + val + "'";
					html += " onchange=\"set_pt_val(this,'" + pp['code'] + "','" + flds[row] + "');\">";
					
				}
			}
			
		}
		html += "</tr>";
	}
	html += '</table>';
	document.getElementById('pt_settings').innerHTML = html;		
	
}
// globals :-(
var pt_time_fld = null;
var pt_time_code = null;
function set_pt_time(def,code,fld,hd)
{
	console.log('set time def = ' + def + ' pt:' + code + ' fld:' + fld);
	pt_time_fld = fld;
	pt_time_code = code;
	document.getElementById('pt_time_hd').innerHTML = hd;
	document.getElementById('pt_time_code').innerHTML = code;
	var hours = def / 60;
	var minutes = def % 60;
	document.getElementById('pt_time_hours').value = hours;
	document.getElementById('pt_time_minutes').value = minutes;
	show ('edit_pt_time_div');
}

function save_pt_time()
{
	hide('edit_pt_time_div');
	var minutes = parseInt(document.getElementById('pt_time_minutes').value) + 60 * parseInt(document.getElementById('pt_time_hours').value);
	$.post("REST/update_pt.php",
		    {
		        code: pt_time_code,
		        fld: pt_time_fld,
		        value: minutes
		    },
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		        load_show_preptypes();
		    });
}
function calc_max_serves()
{
	// if only 2 serving sizes the split is 80 - 20
	let min1 = 20; // minimum percentage of units in two serving size split
	let min2_1 = 10;// minimum percentage of units in three serving size split
	let min2_2 = 30;
	
	var nserves = document.getElementById('serve_no').value;
	var ss1 = document.getElementById('max_serve_in1').value;
	var ss2 = document.getElementById('max_serve_in2').value;
	console.log('calc_max_serves_2',nserves,ss1,ss2);
	var ret = Array();
	if (nserves == 0 || ss1 == 0) {
		console.log('silly values');
		return(null);
	}
	if (!ss2 || ss2 < 1) {
		
		console.log('80 - 20 split');
		let units = Math.floor(nserves * min1 / 100);
		let rem = nserves - units; // assign the min number of units
		let ss1_no = Math.floor (rem / ss1);
		console.log ('assigned units :' + units + ' s1 ' + ss1_no);
		units += rem - (ss1_no * ss1);
		console.log ('final assigned units :' + units + ' s1 ' + ss1_no);
		ret.ss1 = ss1;
		ret.ss1_no = ss1_no;
		ret.units = units;
		
		// 
	}
	else {
		console.log('60 -30 - 10 split');
		console.log('80 - 20 split');
		let units = Math.floor(nserves * min2_1 / 100);
		
		let rem = nserves - units; // assign the min number of units
		
		let ss2_no = Math.floor(nserves * min2_2 / 100 / ss2);
		console.log ('assigned units :' + units + ' s1 ' + ss2_no);
		if (ss2_no == 0 && ss2 < rem) ss2_no = Math.floor(rem/ss2);
		rem -= ss2_no * ss2;
		let ss1_no = Math.floor (rem / ss1);
		console.log ('final assigned units :' + units + ' s1 ' + ss1_no);
		units += rem - (ss1_no * ss1);
		ret.ss1 = ss1;
		ret.ss1_no = ss1_no;
		ret.ss2 = ss2;
		ret.ss2_no = ss2_no;
		ret.units = units;
		// div.innerHTML = ss1_no + ' of ' + ss1 + ', ' + ss2_no + ' of ' + ss2 + ', ' + units + ' units';
	}		
	return(ret);
}

function disp_max_serves()
{
	var div = document.getElementById('max_serve_div');
	var ret = calc_max_serves();
	if (ret) {
		if (ret.ss2) 
			div.innerHTML = ret.ss1_no + ' of ' + ret.ss1 + ', ' + ret.ss2_no + ' of ' + ret.ss2 + ', ' + ret.units + ' units';
		else
			div.innerHTML = ret.ss1_no + 'of ' + ret.ss1 + ', ' + ret.units + ' units';
	}
}
</script>

<div class='acs_main'>
<div class='popup' id='edit_pt_time_div'>
<h2 id='pt_time_hd'></h2>
<div>Preptype: <div id='pt_time_code'></div></div>
<input type='number' id='pt_time_hours'>:<input type='number' id='pt_time_minutes'>
<table width='100%'>
	<tr><td><button type='button' class='button_main' onclick="hide('edit_pt_time_div');">Cancel</button>
	<td><button type='button' class='button_main' onclick="save_pt_time();">Save</button></td>
	</tr>
</table>
</div>
<div class='acs_container' id='pt_settings' >



</div>
<div class='acs_container'>
<div>
	<h1>Max Serve Calculator</h1>
	<table>
	<tr><td>Number of serves :</td><td><input id='serve_no' type='number' onchange="disp_max_serves();">
	</td><td rowspan='3'><div id='max_serve_div'></div>
	<tr><td>Serve size 1 :</td><td><input id='max_serve_in1' type='number' onchange="disp_max_serves();">
	<tr><td>Serve size 2 :</td><td><input id='max_serve_in2' type='number' onchange="disp_max_serves();">
	
	</table>
</div>
</div>
</div>

			
