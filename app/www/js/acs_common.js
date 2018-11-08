var plating_items = null; // array of menu_items currently plating
var menu_items = null;
var chefs = null;

function load_plating_items(callback) // load menu_items currently being plated
{
   $.ajax({
    	url: RESTHOME + "get_plating.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
            plating_items = result; // need to populate with descritions
            for (var i = 0; i < plating_items.length; i++) {
            	
            	var menu_item = get_menu_item_by_id(plating_items[i].menu_item_id);
            	plating_items[i].dish_name = menu_item.dish_name;
            	plating_items[i].code = menu_item.code;
            	console.log('loading plating item ' + menu_item.dish_name);
            	for (var j = 0; j < plating_items[i].items.length; j++) {
            		console.log('loading plating item ' + j, plating_items[i].items[j].menu_item_component_id);
            		var comp = get_component_by_id(plating_items[i].items[j].menu_item_component_id);
            		plating_items[i].items[j].description = comp.description;
            	}
            }
            if (callback) callback();
            console.log("got " + result.length + " plating items");           
        },
        fail: (function (result) {
            console.log("fail load_plating_items",result);
        })
    });
}

function get_menu_item_by_id(menu_item_id) {
	// menu_items not a hashed array because the search fn needs it that way
	for (var i = 0; i < menu_items.length; i++) {
		if (menu_items[i].id == menu_item_id) return(menu_items[i]);
	}
	return(null);
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
	return (new Intl.DateTimeFormat('en-AU', options).format(d));
}

function get_component_by_id(id)
{
	for(var i= 0; i < comps.length; i++) {
		if (comps[i].id == id) return (comps[i]);
	}
	return null;
}

function get_component_by_description(description)
{
	for(var i= 0; i < comps.length; i++) {
		if (comps[i].description == description) return (comps[i]);
	}
	return null;
}
function margin(t)
{
	return("<div class='margin10'>" + t + "</div");
}

function zeropad(s,n) // return string length n padded with zeroes
{
	var ret = s;
	while (ret.length < n) ret = '0' + ret;
	return(ret);
}

function get_chef_by_id(id)
{
	if (!chefs) return(null);
	for (var i = 0; i < chefs.length;i++) {
		if (chefs[i].id == id) return chefs[i];
	}
	return(null);
}

function load_chefs(fn)
{
	console.log("loading chefs");
    $.ajax({
    	url: RESTHOME + "get_chefs.php",
        type: "POST",
        dataType: 'json',
        success: function(result) {
            chefs = result;
            
            if (fn) {
            	console.log("calling fn");
            	fn();
            }
            if (typeof(load_plating_teams) != 'undefined') {
            	load_plating_teams();
            
            	add_chef_select('m1_temp_div_chef','m1_chef_id');
            }
            console.log("got " + result.length + " chefs");   
            
        },
        fail: (function (result) {
            console.log("fail load_chefss",result);
        })
    });
}


function show(div_id) 
{
	document.getElementById(div_id).style.display='block';
}

function hide(div_id) 
{
	document.getElementById(div_id).style.display='none';
}
