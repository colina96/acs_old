<script>

var shift_data = null;
function daily_orders()
{
	openPage('ORDERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('daily_orders_div');
	div.innerHTML = '';
	$.ajax({
        url:  "REST/get_shifts.php",
        type: "POST",
        dataType: 'json',	      
        success: function(result) {
            shift_data = result;	          
            console.log("got " + result.length + " shift items");
            show_shift_orders(result);	            
        },
        fail: (function (result) {
            console.log("fail shifts",result);
        })
    });
}

function show_shift_orders(shift_data) // horrible hack TODO - work out what is really needed and write that properly
{
	console.log(shift_data);
	var div = document.getElementById('daily_orders_div');
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	var headings = ['ITEM CODE','ITEM DESCRIPTION','09:30 AM','11:00 AM','05:00 AM'];
	for (var i = 0; i < headings.length; i++) {
		
		var th = document.createElement('th');
		th.innerHTML = headings[i];
		tr.appendChild(th);
	}
	tab.appendChild(tr);
	for (var i = 0; i < shift_data.length; i++) {
		var tr = document.createElement('tr');
		var td = document.createElement('td');
		td.innerHTML = shift_data[i]['code'];
		tr.appendChild(td);
		var td = document.createElement('td');
		td.innerHTML = shift_data[i]['dish_name'];
		tr.appendChild(td);
		for (var j = 1; j < 4; j++ ) {
			var s = 's' + j;
			var name = 'S' + shift_data[i]['id'] + '_' + j;
			var td = document.createElement('td');
			var val = shift_data[i][s] ? shift_data[i][s]: 0;
			td.innerHTML = "<input type='number' name='" + name + "' value='" + val + "' onchange='set_shift_qty(" + shift_data[i]['id'] + "," + j + ");'>";
			tr.appendChild(td);
		}
		tab.appendChild(tr);
	}
	div.appendChild(tab);
	
}

function set_shift_qty(menu_item_id,shift_id)
{
	console.log('set_shift_qty',menu_item_id,shift_id);
	var name = 'S' + menu_item_id + '_' + shift_id;
	var val = document.getElementsByName(name)[0].value;
	console.log(val);
	
		$.post("REST/update_shift_data.php",
			    {
			        menu_item_id: menu_item_id,
			        shift_id: shift_id,
			        val: val
			    },
			    function(data, status){
			        console.log("Data: " + data + "\nStatus: " + status);
			    });
	
		
}
</script>
<div class='acs_main'>
<div id='daily_orders_div'></div>
</div>