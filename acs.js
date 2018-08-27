
function openPage(pageName, elmnt, color,content_class,tab_class) {
	// console.log("opening page ",pageName,content_class);
    // Hide all elements with class="tabcontent" by default */
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName(content_class);
    for (i = 0; i < tabcontent.length; i++) {
    	// console.log("found tab ",tabcontent[i].id);
        tabcontent[i].style.display = "none";
    }

    // Remove the background color of all tablinks/buttons
    tablinks = document.getElementsByClassName(tab_class);
    // console.log("found tablinks ",tablinks.length);
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }

    // Show the specific tab content
    document.getElementById(pageName).style.display = "block";

    // Add the specific color to the button used to open the tab content
    // elmnt.style.backgroundColor = color;
}

function add_menu_component(menu_id,menu_item_id,menu_name) 
{
	console.log("add_menu_component ",menu_item_id);
	document.getElementById('add_menu_component_modal').style.display = "block";
	var div = document.getElementById('menu_item_component_div');
	div = document.getElementById('menu_item_name_div');
	div.innerHTML = menu_name;
	var menu_id_val = document.getElementsByName('cc_menu_id')[0];
	menu_id_val.value = menu_id;
	var menu_item_id_val = document.getElementsByName('cc_menu_item_id')[0];
	menu_item_id_val.value = menu_item_id;
}

function del_menu_component(menu_id,menu_item_component_id,description) 
{
	console.log("del_menu_component ",menu_item_component_id,description);
	document.getElementById('del_menu_component_modal').style.display = "block";
	
	var div = document.getElementById('menu_item_name_div');
	// div.innerHTML = menu_name;
	var menu_id_val = document.getElementsByName('cc_menu_id')[0];
	menu_id_val.value = menu_id;
	var menu_item_id_val = document.getElementsByName('cc_menu_item_component_id')[0];
	menu_item_id_val.value = menu_item_component_id;
	var menu_item_id_val = document.getElementById('menu_item_component_description');
	menu_item_id_val.innerHTML = description;
}


function close_menu_component_modal(id) 
{
	document.getElementById(id).style.display = "none";
	
}

function get_comp_by_id(id,fld)
{
	for (var i = 0; i < comps.length; i++) {
		if (comps[i].id == id) {
			var x = comps[i];
			// console.log("comps length ",x.length);
			console.log ("found comp",id,fld,comps[i].description,parseInt(comps[i].prep_type),'!');
			if (comps[i].prep_type == '') { // TODO - set default prep_type to 1 (CC) not ideal.....
				console.log('setting def prep_type');
				comps[i].prep_type = 1;
			}
			return(comps[i][fld]);
		}
	}
	return(1);
}

function format_minutes (min)
{
	min = Math.abs(min)
	 var hours   = Math.floor(min / 60);
	 var minutes = Math.floor(min - (hours * 60));
	 if (hours   < 10) {hours   = "0"+hours;}
	 if (minutes < 10) {minutes = "0"+minutes;}
	 return hours+':'+minutes;
}

function get_preptype_val(id,fld)
{
	for (var i = 0; i < preptypes.length; i++) {
		if (preptypes[i].id == id) {
			return(preptypes[i][fld]);
		}
	}
	return("not found");
}
function load_preptypes()
{
console.log("loading prep types");
    $.ajax({
        url: "REST/get_preptypes.php",
        type: "POST",
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            preptypes = result;
            
            console.log("got " + result.length + " preptypes");
            
        },
        done: function(result) {
            console.log("done preptypes ");
        },
        fail: (function (result) {
            console.log("fail preptypes",result);
        })
    });
}

function load_menu_items(menu_id)
{
	
	console.log("loading menu items",menu_id);
    $.ajax({
        url: "REST/get_menu_items.php",
        type: "POST",
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            menu_items = result;
            $('#search_menu').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: menu_items,
        		// Once a value in the drop down list is selected, do the following:
                select: function(event, ui) {
                    // place the person.given_name value into the textfield called 'select_origin'...
                    $('#search_menu').val(ui.item.label);
                    // and place the person.id into the hidden textfield called 'link_origin_id'. 
                 	console.log('selected ',ui.item.value);
                 	show_menu_item_components(ui.item.value);
                    return false;
                }
        	
            })
            console.log("got " + result.length + " menu itemss");
            
        },
        done: function(result) {
            console.log("load_menu_items");
        },
        fail: (function (result) {
            console.log("fail load_menu_items",result);
        })
    });
}

/*
 * need to rewrite user interface using REST - quick hack for now
 */

function user_label(uid,firstname,lastname)
{
	var user = new Object();
	user.id = uid;
	user.firstname = firstname;
	user.lastname = lastname;
	var data =  {data: JSON.stringify(user)};
    console.log("Sent Off: %j", data);
    
    $.ajax({
        url: 'REST/user_label.php',
        type: "POST",
        data: data,

        success: function(result) {
            console.log("user_label result ",result);
            
        },
        done: function(result) {
            console.log("user_label result ",result);
        },
        fail: (function (result) {
            console.log("user_label fail ",result);
        })
    });
    
}

function load_comps()
{
console.log("loading menu item components");
    $.ajax({
        url: "REST/get_comps.php",
        type: "POST",
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

function new_td(content,classname) {
	var td = document.createElement('td');
	td.className = classname;
	td.innerHTML = content;
	return(td);
}

function show_time(d)
{
	options = {
		hour: 'numeric', minute: 'numeric',

	};
	return (new Intl.DateTimeFormat('en-AU', options).format(d));
}
function show_date(d)
{
	options = {
		day: 'numeric', month: 'numeric',year: 'numeric',

	};
	try {
		var date_str = new Intl.DateTimeFormat('en-AU', options).format(d);
		return (date_str);
	}
	catch (e) {
		console.log(e);
	}
	return('invalid date');
}

function get_component_by_id(id)
{
	for(i= 0; i < comps.length; i++) {
		if (comps[i].id == id) return (comps[i]);
	}
	return null;
}
