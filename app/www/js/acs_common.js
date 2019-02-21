var plating_items = null; // array of menu_items currently plating
var menu_items = null;
var chefs = null;
var plating_teams = null;

function load_menu_items()
{
	let tag = 'loading_menu_items: ';
	console.log(tag, RESTHOME + "get_menu_items.php");
    $.ajax({
    	url: RESTHOME + "get_menu_items.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            menu_items = result;
            find_plating_teams(menu_items); // see what plating teams are needed
            console.log(tag,"got menu_items" + menu_items.length);
            $('#search_menu').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: menu_items,
        		// Once a value in the drop down list is selected, do the following:
                select: function(event, ui) {
                	
                    // place the person.given_name value into the textfield called 'select_origin'...
                    $('#search_menu').val(ui.item.label);
                    // and place the person.id into the hidden textfield called 'link_origin_id'. 
                 	console.log(tag,'selected ',ui.item.value);
                 	show_menu_item_components(ui.item.value);
                    return false;
                }
        	
            });
            console.log(tag,"got " + result.length + " menu items");
        },
        done: function(result) {
            console.log(tag,'done');
        },
        fail: function (result) {
            console.log(tag,"fail",result);
        }
    });
}

function find_plating_teams(menu_items)
{
	console.log('searching for assigned plating teams ',menu_items.length);
	
	if (plating_teams == null) plating_teams = [];
	for (var i = 0; i < menu_items.length; i++) {
		// console.log("item ",menu_items[i]['code'],menu_items[i]['plating_team']);
		if (menu_items[i]['plating_team'] != '') {
			console.log("item ",menu_items[i]['code'],menu_items[i]['plating_team']);
			var pt = menu_items[i]['plating_team'];
			if (typeof plating_teams[pt] == 'undefined') {
				plating_teams[menu_items[i]['plating_team']] = [];
			}
		}
	}
	var d = document.getElementById('plating_teams_list');
	if (d) {
		d.innerHTML = '';
	
		var select = document.createElement('select');
		select.name = 'sel_pt';
		console.log('found plating teams ',plating_teams.length);
		for (var i = 0; i < plating_teams.length; i++) {
			if (plating_teams[i]) {
				 option = document.createElement( 'option' );
				 option.value = i;
				 option.textContent =  'Team ' + i;
			    select.appendChild( option );
			}
			d.appendChild(select);
		}
	}
	load_chefs(null);
}
function get_search_terms()
{
	let tag = 'get_search_terms: ';
	// check elements exist
	// console.log('get_search_terms');
	if (!document.getElementById('report_start') ||
			!document.getElementById('report_end') ||
			!document.getElementById('report_search')) {
		console.log(tag,'search elements do not exist');
		return null;
	}
	let search_terms = new Object();
	
	search_terms.start_date = document.getElementById('report_start').value;
	search_terms.end_date = document.getElementById('report_end').value;
	search_terms.search_for = document.getElementById('report_search').value;
	search_terms.all = true;
	console.log(tag,'search_terms:',search_terms);
	let data =  {data: JSON.stringify(search_terms)};
	return (data);
}

function load_plating_items(callback) // load menu_items currently being plated
{
	let tag = 'load_plating_items: ';
	let data = get_search_terms();
	console.log(tag, data);
	$.ajax({
		url: RESTHOME + "get_plating.php",
		type: "POST",
		dataType: 'json',
		data: data,
		success: function (result) {
			plating_items = result; // need to populate with descritions
			console.log(tag,'success: ',result);
			if (!result || result.error) {
				console.log(tag,'forcing reload');
				location.reload(true);
			}
			for (let i = 0; i < plating_items.length; i++) {

				let menu_item = get_menu_item_by_id(plating_items[i].menu_item_id);
				plating_items[i].dish_name = menu_item.dish_name;
				plating_items[i].code = menu_item.code;
				console.log(tag,'loading plating item ' + menu_item.dish_name);
				for (let j = 0; j < plating_items[i].items.length; j++) {
					console.log(tag,'loading plating item ' + j, plating_items[i].items[j].menu_item_component_id);
					let comp = get_component_by_id(plating_items[i].items[j].menu_item_component_id);
					plating_items[i].items[j].description = comp.description;
				}
			}
			if (callback) callback();
			console.log(tag,"got " + result.length + " plating items");
		},
		fail: function (result) {
			console.log(tag,"fail: ", result);
		}
	});
}

function get_menu_item_by_id(menu_item_id) {
	// menu_items not a hashed array because the search fn needs it that way
	for (var i = 0; i < menu_items.length; i++) {
		if (menu_items[i].id == menu_item_id) return(menu_items[i]);
	}
	console.log('ERROR - could not find menu_item id',menu_item_id,menu_items.length);
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
	console.log(comps);
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
	return("<div class='m-10'>" + t + "</div");
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
            if (!result || result.error ) {
            	console.log('forcing reload');
            	location.reload(true);
            }
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
            console.log("fail load_chefs",result);
        })
    });
}


function show(div_id) 
{
	document.getElementById(div_id).style.display='block';
	console.log('show ' + div_id);
	if (document.getElementById('shield')) {
		console.log('showing shield');
		document.getElementById('shield').style.display='block';
	}
}

function hide(div_id) 
{
	document.getElementById(div_id).style.display='none';
	if (document.getElementById('shield')) {
		document.getElementById('shield').style.display='none';
	}
}


