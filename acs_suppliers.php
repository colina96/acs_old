<div class='top_menu_container' id="suppliers_subtabs">
			<div class='top_menu'  onclick="suppliers(this)">SUPPLIERS</div>
			<div class='top_menu'   onclick="load_purchase_orders(this)">PURCHASE ORDERS</div>
		<!-- 	<div class='top_menu'   onclick="load_dock_components(this)">ORDER ITEMS</div> -->
			<div class='top_menu'   onclick="goto_new_po(this)">NEW PURCHASE ORDER</div>



</div>
<div class='acs_main' id="suppliers_frame">


	<div id='suppliers_container' class='acs_container'>
    suppliers stuff
	</div>

	<div id='csv_upload_div' style='display:none'>
		<input type='file' accept='text/plain' onchange='open_suppliers_csv(event)'><br>

	</div>
	<div id='csv_errors'></div>
</div>
<div class='acs_main' id="po_frame">
	<div id='po_container' class='acs_container'></div>
</div>
<div class='acs_main' id="new_po_frame">
	<div id='new_po_container' class='acs_container'></div>
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
var supplier_obj = null;
var po_data = null; // purchase orders
var purchase_order = null;
var upload_suppliers_obj = null;



//var open_suppliers_csv = function(event) {
function open_suppliers_csv(event) {
		var input = event.target;
	//	upload_suppliers_obj = new evoz_tools('SUPPLIERS','suppliers_container',data_formats['SUPPLIERS']);

	    console.log('open_suppliers_csv');

	    var reader = new FileReader();
	    reader.onload = function(){
	      var text = reader.result;
	      var json = CSVToArray(text);
	   //   upload_suppliers_obj.json = json;
	      console.log(json);
	    //  upload_suppliers_obj = array();
	   //   upload_suppliers_obj.raw_data = json;
	      /*
	      chain of events - get existing suppliers and components then insert new values where needed
	      */
	      process_suppliers_csv(json);
	    	show_json(json,'suppliers_container');

			// document.getElementById('output').innerHTML = text;
	      console.log(reader.result.substring(0, 200));
	    };
	    reader.readAsText(input.files[0]);
	  };

function process_suppliers_csv(json)
{
	// quick and dirty to meet deadline - rewrite to make REST interface more generic
	var ret = Object();

	ret.json = json;
	console.log(ret);
	var postdata =  {data: JSON.stringify(ret)};
	$.ajax({
    	url: RESTHOME + "upload_suppliers.php",
        type: "POST",
        // dataType: 'json',
        data: postdata,
        success: function(result) {
            console.log('got result');
            console.log(result);
            document.getElementById('csv_errors').innerHTML = result;

        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
}

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
//	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = openPage('suppliers_frame', this, 'red','acs_main');
	var div = document.getElementById('suppliers_container');
	div.innerHTML = null;

	suppliers_obj = new evoz_tools('SUPPLIERS','suppliers_container',data_formats['SUPPLIERS']);

	// openPage('PARAMS', this, 'red','tabcontent','tabclass');
	suppliers_obj.build_form();
	document.getElementById('csv_upload_div').style.display = 'block';
}

/*
 * function new_po
 * open the form for a new purchase order
 */
function goto_new_po() 
{
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = openPage('new_po_frame', this, 'red','acs_main');
	var div = document.getElementById('new_po_container');
	div.innerHTML = null;
	/* bootstrap layout might work here - use tables to get started */
	var d = document.createElement('div');
	var tab = document.createElement('table');
	var tr = document.createElement('tr');
	/*	tr.setAttribute(
				"onclick",
				"show_dock_component(" + i + "," + j + ");"
		); */
	// reference from acs.js : new_td(content, classname)
	tr.appendChild(new_td('SUPPLIER :','comp','m-5'));
	tr.appendChild(new_td_text_input('supplier_input','comp','td_input',''));
	tr.appendChild(new_td('NOTES :','comp','m-5'));
	tr.appendChild(new_td_text_input('notes_input','comp','td_input',''));
	tab.appendChild(tr);
	d.appendChild(tab);
	div.appendChild(d);
	// add purchare order items
	var tab = document.createElement('table');
	tab.id = 'po_item_table';
	var tr = document.createElement('tr');
	tr.appendChild(new_td('ITEM_NAME :','comp','m-5'));
	// tr.appendChild(new_td('ITEM_CODE :','comp','m-5')); not used ....yet
	tr.appendChild(new_td('SPEC :','comp','m-5'));
	tr.appendChild(new_td('UOM :','comp','m-5'));
	tab.appendChild(tr);
	var tr = document.createElement('tr');
	tr.appendChild(new_td_text_input('po_item_name','comp','td_input',''));
	tr.appendChild(new_td_text_input('po_spec','comp','td_input',''));
	tr.appendChild(new_td_text_input('po_uom','comp','td_input',''));
	tab.appendChild(tr);
	
	div.appendChild(tab);
	// tr.appendChild(new_td('SHELF LIFE :','comp','m-5'));
	get_db_data('SUPPLIERS','',setup_supplier_search);
	get_db_data('MENU_ITEM_COMPONENTS','',setup_po_comp_search);
}

function setup_po_comp_search(data)
{
	console.log('setup_po_comp_search',data);
	var items = new Array();
	for (let i = 0; i < data.data.length; i++) {
		let s = new Object();
		s.label = data.data[i].description;
		s.value = data.data[i].id;
		items.push(s);
	}
	setup_search('po_item_name',items);
}

function setup_supplier_search(data)
{
	console.log('setup_supplier_search',data);
	var items = new Array();
	for (let i = 0; i < data.data.length; i++) {
		let s = new Object();
		s.label = data.data[i].name;
		s.value = data.data[i].id;
		items.push(s);
	}
	setup_search('supplier_input',items);
}
function setup_search(fld,data)
{
	$('#' + fld).autocomplete({
		// This shows the min length of charcters that must be typed before the autocomplete looks for a match.
		minLength: 2,
		source: data,
		response: function (event, ui) {
			console.log("search response found " + ui.content.length); console.log(ui);
			
		},
		select: function (event, ui) {			
			$('#' + fld).val(ui.item.label);		
			console.log('setup_search: selected ', ui.item.value);
			return false;
		}
	})
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


function load_purchase_orders()
{
	let tag = 'purchase_orders: ';
	console.log(tag,"will update ");
    $.ajax({
        url: RESTHOME + "get_dock.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
        	console.log(result);
            // comps = result;
        	purchase_orders = result.purchase_orders;
        	// supplier_obj.data = result.suppliers;
            // TODO - make search work

            console.log(tag,"got " + result.length + " comps");
            show_purchase_orders(purchase_orders);
        },

        fail: (function (result) { console.log(tag,"fail ",result);})
    });

}


function show_purchase_orders()
{
	
	openPage('SUPPLIERS', this, 'red','tabcontent','tabclass');
	var div = openPage('po_frame', this, 'red','acs_main');
	var div = document.getElementById('po_container');
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
	tr.appendChild(new_th('LABEL','comp','m-5'));
	//tr.appendChild(new_th('EDIT','comp','m-5')); 	<< This one is a column for the Save button

	table.appendChild(tr);
	for (var i in purchase_orders) {
		console.log('purchase_order',i);
		console.log(purchase_orders[i]);
		console.log('purchase_orders[i].items.length',purchase_orders[i].items.length);
		for (var j = 0; j < purchase_orders[i].items.length; j++) {
			console.log('item',j);
			var tr = document.createElement('tr');
		/*	tr.setAttribute(
					"onclick",
					"show_dock_component(" + i + "," + j + ");"
				); */
			// reference from acs.js : new_td(content, classname)
			tr.appendChild(new_td((j == 0)?purchase_orders[i].supplier.name:'','comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].item_code,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].component.description,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].spec,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].UOM,'comp','m-5'));
			tr.appendChild(new_td(purchase_orders[i].items[j].open_shelf_life,'comp','m-5'));

//			tr.appendChild(new_td(purchase_orders[i].items[j].PT.code,'comp','m-5'));
			tr.appendChild(new_td(
				select_prep_type(i,j),
				'comp','m-5'));
		//	var poi_id = purchase_orders[i].items[j].id;
			var menu_item_component_id = purchase_orders[i].items[j].menu_item_component_id;
			var innerHTML = "<input type='checkbox' value='1' name='label_at_dock_" + poi_id + "' onclick='set_db_field(this,\"MENU_ITEM_COMPONENTS\",\"label_at_dock\"," + menu_item_component_id + ");'";
        	if (purchase_orders[i].items[j].component.label_at_dock == 1) {
            	innerHTML += ' checked';
        	}
        	innerHTML += '>';
        	tr.appendChild(new_td(innerHTML,'comp','m-5'));
			// tr.appendChild(new_td(purchase_orders[i].items[j].label_at_dock,'comp','m-5'));
			// This one is a SAVE button element	
			// tr.appendChild(new_td(
			// 	edit_or_save(i,j),
			// 	'comp','m-5'));
			
			table.appendChild(tr);
		}
	}
	div.appendChild(table);
	div.appendChild(new_purchase_order_form());
}

// Function taken from menu.php and modified to simplify creating a Dropdown to choose prep type
// i and j are coordinates
function select_prep_type(i, j){

	item = purchase_orders[i].items[j];
	poi_id = item.id;
	pt_code = item.PT.code;

	console.log("select prep type", item);

	var ret =  "<select name='pt_" + 6 + "' onchange='update_prep_type(this,"+ poi_id +");'>";
	var idx = 1;
	// need to make a call and load from DB
	var preptypes = ['CC', 'HF', 'ESL', 'LR', 'AHR', 'FRESH','FROZEN','DRY', 'DECANT'];
	for (var i in preptypes) {
		ret += "<option value='" + i + "'";
		if (preptypes[i] == pt_code) { ret += " selected"; }
		ret += ">" + preptypes[i] + "</option>";
	}
	ret +=  "</select>";
	return (ret);

}


// We should be able to create standard POST and PATCH, GET and DELETE requests and just pass the data we want to send. 
// It would save a lot of space. Something to do when the time allows.

// Function to update Purchase Order Item prep type, when a different value is chosen in Drop Down. 
function update_prep_type(s,id){
	
	// var s = document.getElementById("pt_" + comp_id);
	
	var idx = s.selectedIndex + 1; // temp fix to pass the PT code. In the array it starts from 0, but in DB it starts with 1
	var val = s.options[idx].value;
	console.log("update PO Item ",id,idx,val);
	
	$.post(RESTHOME + "POI" + "/update_pois.php",
		    {
		        poi_id: id,
		        pt_id: idx
			},
		    function(data, status){
		        console.log("Data: " + data + "\nStatus: " + status);
		    });
}

function edit_or_save(i, j){
	var ret = "<button type='button' class='button_main' \
		onclick='update_purchase_order_item("+i+","+j+");'>Save</button> ";
	console.log(ret);
	return (ret);
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
	for (var s in supplier_obj.data) {
		console.log(supplier_obj.data[s]);
		var opt = document.createElement('option');
		opt.innerHTML = supplier_obj.data[s].name;
		opt.value = s;
		select.appendChild(opt);

	}
	div.appendChild(select);
	return(div);
}

	</script>
