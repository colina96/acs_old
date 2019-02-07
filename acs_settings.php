<div class='acs_main'>
<div class='acs_container' id='pt_settings' >
<script>
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
				if (!min || min < 1) {
					html += "<td>-</td>";
				}
				else if (min < 60) {
					html += "<td>" + val + " minutes</td>";
				}
				else {
					var hrs = min / 60;
					if (hrs > 1) {
						html += "<td>" + hrs + " hours</td>";
					}
					else {
						html += "<td>" + hrs + " hour</td>";
					}
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

</script>
<div onclick='load_show_preptypes();'>LOAD</div>
<?php

//show_prep_types();
?>
</div>
</div>
<?php 
function show_prep_types()
{
	$sql = "select * from PREP_TYPES order by ID";
	$rows = array (
			"PREP TYPE" => "code",
			"Q-Pack DATA<br>RANGE OFFSET" => "days_offset",
			"MILESTONE 1<br>(TEMPERATURE)" => "M1_temp",
			"MILESTONE 2<br>(TIME)" => "M2_time_minutes",
			"MILESTONE 2<br>(TEMPERATURE)" => "M2_temp",
			"MILESTONE 3<br>(TIME)" => "M3_time_minutes",
			"MILESTONE 3<br>(TEMPERATURE)" => "M3_temp",
			"SHELF LIFE" => "shelf_life_days",
			"SENSOR" => "probe_type",
			"ALARM TIME M2" => "M2_alarm_min",
			"ALARM TIME M3" => "M3_alarm_min",
	);
	$prep_types = array();
	$result = mysql_query($sql);
	if ($result) {
		while($data = mysql_fetch_array($result))
		{
			$prep_types[] = $data;
		}
		echo ("<div class='m-10'>");
		echo ("<table border=0 width='100%' class='table' id='settings'>");
		$rownum = 1;
		foreach ($rows as $row => $fieldname) {
			// $row_class = ($rownum%2)?'even_tr':'odd_tr';
			
			// echo ("<tr class='".$row_class."'><th>".$row."</th>");
            echo ("<tr><th>".$row."</th>");
			foreach ($prep_types as $prep => $val) {
				if (strpos($row,"TIME") > 0) {
					$min = $val[$rows[$row]];
					if ($min < 1) {
						echo ("<td>-</td>");
					}
					else if ($min < 60) {
						echo ("<td>".$val[$rows[$row]]." minutes</td>");
					}
					else {
						$hrs = $min / 60;
						if ($hrs > 1) {
							echo ("<td>".$hrs." hours</td>");
						}
						else {
							echo ("<td>".$hrs." hour</td>");
						}
					}
				}
				else {
					if ($rownum == 1) {
						echo ("<th>".$val[$rows[$row]]."</th>");
					}
					else {
						print "<th><input class='pt_edit' onchange='set_pt_val(this,'";
						print $fieldname."','".$val['code']."');' value='".$val[$rows[$row]]."'></th>";
						//echo ("<td>".$fieldname.'-'.$val['code']." - ".$val[$rows[$row]]."</td>");
					}
				}
			}
			echo "</tr>\n";
			$rownum++;
		}
		echo ("</table></div>");
	}
}
			
?>