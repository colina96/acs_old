
function openPage(pageName, elmnt, color,content_class,tab_class) {
	console.log("opening page ",pageName,content_class);
    // Hide all elements with class="tabcontent" by default */
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName(content_class);
    for (i = 0; i < tabcontent.length; i++) {
    	console.log("found tab ",tabcontent[i].id);
        tabcontent[i].style.display = "none";
    }

    // Remove the background color of all tablinks/buttons
    tablinks = document.getElementsByClassName(tab_class);
    console.log("found tablinks ",tablinks.length);
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

