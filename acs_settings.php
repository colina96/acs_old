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
				else if (!val) {
					html += "<td>-</td>";
				}
				else {
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
</div>

			
