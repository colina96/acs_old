<div class='acs_main'>
<div class='acs_container'>
<?php

show_prep_types();
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
		echo ("<table border=1 width='100%'>");
		foreach ($rows as $row => $fieldname) {
			echo ("<tr><td>".$row."</td>");
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
						echo ("<td>".$hrs." hours</td>");
					}
				}
				else {
					echo ("<td>".$val[$rows[$row]]."</td>");
				}
			}
			echo "</tr>\n";
		}
		echo ("</table>");
	}
}
			
?>