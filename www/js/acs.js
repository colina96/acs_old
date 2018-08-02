var default_tab='m_current_tracking_tab';

function goto_m_main()
{
	openPage('mm2', this, 'red','mobile_main','tabclass');
	m_tracking();
}
function component_selected(id)
{
	openPage('m_temp', this, 'red','mobile_main','tabclass');
	var comp = get_component_by_id(id);
	var prep_type_id = comp['prep_type'];
	console.log('prep_type_id',prep_type_id);
	if (prep_type_id < 1) prep_type_id = 1;
	var prep_type_val = get_preptype_val(prep_type_id,'M1_temp');
	document.getElementById('chk_temp_item_div').innerHTML = comp['description'];
	document.getElementById('chk_temp_item_time_div').innerHTML = 'M1 REQUIRED: > ' + prep_type_val;
}
function m_tracking()
{
	console.log('goto_active_components');
	openPage('m_current_tracking', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	 $.ajax({
	        url: "http://10.0.0.32/acs/REST/get_active_comps.php",
	        type: "POST",
	        dataType: 'json',
	        // contentType: "application/json",
	        success: function(result) {
	            active_comps = result;
	           // document.getElementById('active_comps').innerHTML = result;
	            console.log("got " + result.length + " comps");
	            m_show_active_components(result);
	            
	        },
	        done: function(result) {
	            console.log("done load_comps ");
	        },
	        fail: (function (result) {
	            console.log("fail load_comps",result);
	        })
	    });
}

function m_show_active_components(data)
{
	var div = document.getElementById('m_current_tracking');
	if (data.length < 1) {
		div.innerHTML = "<h1>No Active Components</h1>";
		return;
	}
	div.innerHTML = "<h1>Active Components</h1>";
	var tab = document.createElement('table');
	tab.className = 'component_table';
	var tr = document.createElement('tr');
	
	
    tr.appendChild(new_td('Description','comp'));   
    tr.appendChild(new_td('M','comp'));
    
    tr.appendChild(new_td('TIME','comp'));
   	tab.appendChild(tr);
   	for (i=0; i<data.length; ++i) {
   		var tr = document.createElement('tr');
   		tr.appendChild(new_td(data[i]['description'],'comp'));
   		
   		var M1_time = new Date(data[i]['M1_time']);
   		var M2_time = new Date(data[i]['M2_time']);
   		console.log("M2 time -",data[i]['M2_time'],"-");
   		if (data[i]['M2_time'] == '') {
   			tr.appendChild(new_td('<div class="m_bluedot">2</div>','comp'));
   		}
   		else {
   			tr.appendChild(new_td('<div class="m_bluedot">2</div>','comp'));
   		}
   		// var M1_t = M1_time.getHours() + ":" + M1_time.getMinutes();
   		tr.appendChild(new_td(show_time(M1_time),'comp'));
   		// tr.appendChild(new_td(data[i]['M1_time'],'comp'));
   		
   		  		tab.appendChild(tr);
    }
   	div.appendChild(tab);
}

function load_comps()
{
console.log("loading menu item components");
    $.ajax({
        url: "http://10.0.0.32/acs/REST/get_comps.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            comps = result;
            $('#search').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: comps,
        		select: function(event, ui) {
                    // place the person.given_name value into the textfield called 'select_origin'...
                    $('#search').val(ui.item.label);
                    // and place the person.id into the hidden textfield called 'link_origin_id'. 
                 	console.log('selected ',ui.item.value);
                 	component_selected(ui.item.value);
                 	cordova.plugins.Keyboard.close();
                 	$('#search').blur();
                    return false;
                }  
            })
            console.log("got " + result.length + " comps");
            
        },
        done: function(result) {
            console.log("done load_comps ");
        },
        fail: (function (result) {
            console.log("fail load_comps",result);
        })
    });
}
