<div class='top_menu_container'>
			<div class='top_menu'  onclick="suppliers(this)">SUPPLIERS</div>
			<div class='top_menu'   onclick="load_purchase_orders(this)">PURCHASE ORDERS</div>
			<div class='top_menu'   onclick="load_dock_components(this)">ORDER ITEMS</div>
		
			
			
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
	form_layout['SUPPLIERS'] = {
		'XX-fields' : {
			'name' : { 'Title':'SUPPLIER' },
			'pvalue' : { 'Title':'VALUE' },
		},
		'className' : 'menu_table',
	};
	form_layout['MENU_ITEM_COMPONENTS'] = {
			'XX-fields' : {
				'name' : { 'Title':'SUPPLIER' },
				'pvalue' : { 'Title':'VALUE' },
			},
			'className' : 'menu_table',
			'title' : 'Components to label at dock',
		};
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('suppliers_container');
	div.innerHTML = null;
	
	var e = new evoz_tools('SUPPLIERS','suppliers_container',data_formats['SUPPLIERS']);
	
	// openPage('PARAMS', this, 'red','tabcontent','tabclass');
	e.build_form();
}

function load_dock_components()
{
	let tag = 'load_dock_components';
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('suppliers_container');
	div.innerHTML = null;
	
	var comp_data = new evoz_tools('MENU_ITEM_COMPONENTS','suppliers_container',null,'label_at_dock = 1');
	var prep_type_data = new evoz_tools('PREP_TYPES',null,null,null);
/*	for (let id in comp_data.data) {
		comp_data.data['PT_CODE'] = 'bananaa';
	} */
	console.log(tag);
	console.log(comp_data);
	// comp_data.fields.push({ 'Field':'PT_CODE' ,'Type':'varchar(20)' });
//	console.log(comp_data);
	// openPage('PARAMS', this, 'red','tabcontent','tabclass');
	comp_data.build_form();
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
        	supplier_data = result.suppliers;
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
	table.className = 'menu_table';
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
	div.appendChild(new_purchase_order_form());
}

function new_purchase_order_form()
{
	/* two part form - create purchase order and list of items. 
	*/
	let tag = 'new_purchase_order_form';
	console.log(tag);
	var div = document.createElement('div');
	var select = document.createElement('select');
	select.id = 'select_supplier';
	for (var s in supplier_data) {
		console.log(supplier_data[s]);
		var opt = document.createElement('option');
		opt.innerHTML = supplier_data[s].name;
		opt.value = s;
		select.appendChild(opt);
		
	}
	div.appendChild(select);
	return(div);
}

	</script>

