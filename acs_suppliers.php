<div class='top_menu_container'>
			<div class='top_menu'  onclick="suppliers(this)">SUPPLIERS</div>
			<div class='top_menu'   onclick="load_purchase_orders(this)">PURCHASE ORDERS</div>
		
			
			
</div>
<div class='acs_main' id="suppliers_frame">

	
<div id='suppliers_container' class='acs_container'>
    suppliers stuff
</div>
</div>

<script>
var data_formats = { // map csv columns to database fields
		'SUPPLIERS' : {
			1:'name',
			2:'address1',
			3:'address2',
			4:'phone'
		}
			
}
var supplier_data = null;
var po_data = null; // purchase orders
var purchase_order = null;
function suppliers()
{
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('suppliers_container');
	div.innerHTML = null;
	if (supplier_data == null) {
		supplier_data = new evoz_tools('SUPPLIERS','suppliers_container',data_formats['SUPPLIERS']);
	}
	// openPage('PARAMS', this, 'red','tabcontent','tabclass');
	supplier_data.build_form();
}

function Xload_purchase_orders()
{
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('suppliers_container');
	div.innerHTML = null;
	if (po_data == null) {
		po_data = new evoz_tools('PURCHASE_ORDERS','suppliers_container',data_formats['PURCHASE_ORDERS']);
	}
	// openPage('PARAMS', this, 'red','tabcontent','tabclass');
	po_data.build_form();
}

function load_purchase_orders()
{
	let tag = 'purchase_orders: ';
	console.log(tag,"loading ");
    $.ajax({
        url: RESTHOME + "get_dock.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
        	console.log(result);
            // comps = result;
        	purchase_orders = result.purchase_orders;
            // TODO - make search work
           
            console.log(tag,"got " + result.length + " comps");
            show_purchase_orders(purchase_orders);
        },

        fail: (function (result) { console.log(tag,"fail ",result);})
    });
	
}

function show_purchase_orders()
{
	var div = document.getElementById('suppliers_container');
	div.innerHTML = '';
	var table = document.createElement('table');
	table.className = 'item_table';
	table.width = '100%';
	var tr = document.createElement('tr');
	tr.appendChild(new_th('SUPPLIER','comp','m-5'));
	tr.appendChild(new_th('ITEM CODE','comp','m-5'));
	tr.appendChild(new_th('ITEM NAME','comp','m-5'));
	tr.appendChild(new_th('SPEC','comp','m-5'));
	tr.appendChild(new_th('UOM','comp','m-5'));
	tr.appendChild(new_th('SHELF LIFE<br>AFTER OPENING','comp','m-5'));
	tr.appendChild(new_th('ITEM TYPE','comp','m-5'));
	
	table.appendChild(tr);
	for (var i in purchase_orders) {
		console.log('purchase_order',i);
		console.log(purchase_orders[i]);
		console.log('purchase_orders[i].items.length',purchase_orders[i].items.length);
		for (var j = 0; j < purchase_orders[i].items.length; j++) {
			console.log('item',j);
			var tr = document.createElement('tr');
			tr.setAttribute(
					"onclick",
					"show_dock_component(" + i + "," + j + ");"
				);
			tr.appendChild(new_td((j == 0)?purchase_orders[i].supplier.name:'','comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].item_code,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].component.description,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].spec,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].UOM,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].open_shelf_life,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].PT.code,'comp','m-5'));
			
			table.appendChild(tr);
		}
		
		
	}
	div.appendChild(table);
}

	</script>

