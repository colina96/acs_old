<script>

var params = null;

function get_params(callback_fn)
{
	$.ajax({
        url:  "REST/get_params.php",
        type: "POST",
        dataType: 'json',	      
        success: function(result) {
            console.log(result);
            params = result;	          
            console.log("get_params got " + result.length + " items");
				if (callback_fn) callback_fn(result);
        },
        fail: (function (result) {
            console.log("fail get_params",result);
        })
    });
}

function settings()
{
	openPage('PARAMS', this, 'red','tabcontent','tabclass');
	var div = document.getElementById('settings_div');
	div.innerHTML = '';
   get_params(show_settings);
	
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
