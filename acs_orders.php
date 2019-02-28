<script>

var shift_data = null;
function daily_orders()
{
    let tag = 'daily_orders: ';
	openPage('ORDERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('daily_orders_div');
	div.innerHTML = '';
	
	$.ajax({
        url:  "REST/get_shifts.php",
        type: "POST",
        dataType: 'json',	      
        success: function(result) {
            shift_data = result;	          
            console.log(tag,"success: got " + result.length + " shift items");
            show_shift_orders(result);	            
        },
        fail: function (result) {
            console.log(tag,"fail",result);
        }
    });
}

function show_shift_orders(shift_data) // horrible hack TODO - work out what is really needed and write that properly
{
    let tag = 'show_shift_orders: ';
	console.log(tag, 'shift_data: ', shift_data);
	var div = document.getElementById('daily_orders_div');
	div.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	var headings = ['ITEM CODE','ITEM DESCRIPTION'];
	var headings2 = ['Today, 09:30 AM','Today, 11:00 AM','Tomorrow, 05:00 AM'];
	for (var i = 0; i < headings.length; i++) {
		
		var th = document.createElement('th');
		// th.rowSpan = 2;
		th.innerHTML = headings[i];
		tr.appendChild(th);
	}
	for (var i = 0; i < headings2.length; i++) {
		
		var th = document.createElement('th');
		th.colSpan = 2;
		th.innerHTML = headings2[i];
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
			var td = document.createElement('td');
			s = s + "_done";
			td.innerHTML = shift_data[i][s] ? shift_data[i][s]: 0;
			
			tr.appendChild(td);
		}
		tab.appendChild(tr);
	}
	div.appendChild(tab);
	var btn = document.createElement('button');
	btn.className= 'button_main';
}

function set_shift_qty(menu_item_id,shift_id) {
    let tag = 'set_shift_qty: ';
	console.log(tag,menu_item_id,shift_id);
	var name = 'S' + menu_item_id + '_' + shift_id;
	var val = document.getElementsByName(name)[0].value;
	console.log(tag,val);

    $.post(
        "REST/update_shift_data.php",
        {
            menu_item_id: menu_item_id,
            shift_id: shift_id,
            val: val
        },
        function (data, status) {
            console.log(tag,"Data: " + data + "\nStatus: " + status);
        }
    );
}
function clear_daily_completed()
{
    let tag = 'clear_daily_completed: ';
	console.log(tag);
	let data =  {clear: 'true'};
	$.ajax({
        url:  "REST/get_shifts.php",
        type: "POST",
        data: data,
        dataType: 'json',	      
        success: function(result) {
            console.log(tag, 'success: '.result);
            shift_data = result;	          
            console.log(tag,"got " + result.length + " shift items");
            show_shift_orders(result);	            
        },
        fail: function (result) {
            console.log(tag, "fail",result);
        }
    });
}
</script>
<div class="menu_buttons">
    <div class="menu_type" id="menu_status">
        
    </div>
    <div class="acs_sidebar">
        <button type='button' class='button_main' href="#" id="clear_qtys_btn"
                onclick="clear_daily_completed()">Clear completed totals</button>
    </div>
</div>
<div class='acs_main' id="oder_frame">
<div class="acs_right_content">
<div id='daily_orders_div' class='overflow'></div>
</div>
</div>