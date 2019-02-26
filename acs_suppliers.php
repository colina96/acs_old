<div class='top_menu_container'>
			<div class='top_menu'  onclick="suppliers(this)">SUPPLIERS</div>
			<div class='top_menu'   onclick="purchase_orders(this)">PURCHASE ORDERS</div>
			<div class='top_menu'   onclick="load_plating_data();">PLATING</div>
			
			
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

function purchase_orders()
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
	</script>

