<script>

var params = null;
function settings()
{
	openPage('PARAMS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('settings_div');
	div.innerHTML = '';
	
	$.ajax({
        url:  "REST/get_params.php",
        type: "POST",
        dataType: 'json',	      
        success: function(result) {
            console.log(result);
            params = result;	          
            console.log("get_params got " + result.length + " items");
            show_settings(result);	            
        },
        fail: (function (result) {
            console.log("fail get_params",result);
        })
    });
}

function acs_submit_form(formid)
{
	console.log('acs_submit_form',formid);
	var iform = document.getElementById('form_' + formid);
	var elements = iform.elements;
	
	if (!iform) console.log('cannot find',formid);
// 	console.log('form elements ' + elements.length);
	var data = Object();
	for (i=0; i<elements.length; i++){
		if (elements[i].type == 'checkbox') data[elements[i].id] = elements[i].checked?1:0;
		else data[elements[i].id] = elements[i].value;
	   //  console.log(elements[i].id,elements[i].type,elements[i].value,elements[i].checked);
	}
	var ret = Object();
	ret.TABLENAME = formid;
	ret.data = data;
	ret.action = 'INSERT';
	console.log(ret);
	var postdata =  {data: JSON.stringify(ret)};
	$.ajax({
    	url: RESTHOME + "replace.php",
        type: "POST",
        // dataType: 'json',
        data: postdata,
        success: function(result) {
            console.log('got result');
            console.log(result);    
            document.getElementById('settings_div').innerHTML = result;
        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
	return(false);
}

var form_layout = { 'USERS': 
	{ 
		'fields' : {
			'email' : { 'Title':'Login'  },
			'password' : { 'Title':'Password'  }
		}
	}
}

function build_form(data)
{
	var div = document.getElementById('settings_div');
	var h1 = document.createElement('H1');
	h1.innerHTML = data.TABLENAME;
	div.appendChild(h1);
	var form = document.createElement('form');
	form.id = 'form_' + data.TABLENAME;
	
	fields = data.fields;
	var tab = document.createElement('table');
	for (let i = 1; i < fields.length; i++) { // ignore id
		var tr = document.createElement('tr');
		var td = document.createElement('td');
		if (form_layout[data.TABLENAME] && 
				form_layout[data.TABLENAME].fields && 
				form_layout[data.TABLENAME].fields[fields[i].Field] &&
				form_layout[data.TABLENAME].fields[fields[i].Field]['Title'])
			td.innerHTML = form_layout[data.TABLENAME].fields[fields[i].Field]['Title'];
		else 
			td.innerHTML = fields[i].Field;
		tr.appendChild(td);
		var td = document.createElement('td');
		if (fields[i].Type.indexOf('varchar') == 0) {
			td.innerHTML = "<input id='" + fields[i].Field + "' type='text'>";
		}
		else if (fields[i].Type == 'tinyint(1)') {
			td.innerHTML = "<input id='" + fields[i].Field + "' type='checkbox' value='1'>";
		}
		else {
			td.innerHTML = fields[i].Field;
		}
		tr.appendChild(td);
		tab.appendChild(tr);
	}
	form.appendChild(tab);
	var submit = document.createElement('div');
	submit.innerHTML = '<div onclick="acs_submit_form(\'' + data.TABLENAME + '\');">Submit</div>';
	// submit.setAttribute("onclick",acs_submit_form);
	
	div.appendChild(form);	
	div.appendChild(submit);
}

function uploads() 
{

	openPage('PARAMS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('settings_div');
	div.innerHTML = '';
	var q = new Object();
	q.TABLENAME = 'USERS';
	
	var data =  {data: JSON.stringify(q)};
	
	console.log(data);
	$.ajax({
    	url: RESTHOME + "replace.php",
        type: "POST",
        dataType: 'json',
        data: data,
        success: function(result) {
            console.log(result);    
            if (result.fields) build_form(result);       
        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
}


function show_settings(params) 
{

	var div = document.getElementById('settings_div');
	div.innerHTML = '';
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	var headings = ['PARMS','VALUE'];
	
	for (var i = 0; i < headings.length; i++) {
		
		var th = document.createElement('th');
		// th.rowSpan = 2;
		th.innerHTML = headings[i];
		tr.appendChild(th);
	}
	tab.appendChild(tr);
	
	for (var key in params) {
		var tr = document.createElement('tr');
		var td = document.createElement('td');
		
		td.innerHTML = key;
		tr.appendChild(td);


		var td = document.createElement('td');
		var val = params[key];
		td.innerHTML = "<input name='" + key + "' value='" + val + "' onchange='set_param(name,this);'>";
		tr.appendChild(td);
		tab.appendChild(tr);
	}
	div.appendChild(tab);
	
}

function set_param(name,input)
{

	
	var val = document.getElementsByName(name)[0].value;
	console.log('update param ',name,val);
	console.log(val);
	
		$.post("REST/update_param.php",
			    {
			        name: name,
			        val: val
			    },
			    function(data, status){
			        console.log("Data: " + data + "\nStatus: " + status);
			        settings(); // reload data to confirm ok
			    });
	
		
}

</script>
<div class="menu_buttons">
    <div class="menu_type" id="settings_status">
        
    </div>
    <div class="acs_sidebar">
    
    </div>
</div>
<div class='acs_main' id="settings_div">
<div class="acs_right_content">
<div id='settings_div' class='overflow'></div>
</div>
</div>