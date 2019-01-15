<?php 
#require('PHPMailer/PHPMailer.php');
#require('PHPMailer/Exception.php');

#use PHPMailer\PHPMailer\PHPMailer;
?>
<script>

var ulbl_id = null;
var ulbl_firstname = null;
var ulbl_lastname = null;
function users()
{
	openPage('USERS', this, 'red');
	load_chefs(show_users);
}
function print_user_label()
{
	user_label(ulbl_id,ulbl_firstname,ulbl_lastname);
	hide('confirm_print_user_label');
}

function center(id)
{
	var div = document.getElementById(id);
	var rect1 = div.getBoundingClientRect();
	var rect2 = div.parentNode.getBoundingClientRect();
	div.style.left = parseInt(rect2.width / 2 - rect1.width/2) + 'px';
}
function conf_user_label(id,firstname,lastname)
{
	document.getElementById('ul1').innerHTML = firstname + ' ' + lastname;
	ulbl_id = id;
	ulbl_firstname = firstname;
	ulbl_lastname = lastname;
	center('confirm_print_user_label');
	show('confirm_print_user_label');
}
function show_users()
{
	var div = document.getElementById('users_div');

	var h = "<table class='table' id='users' width='100%' border='0'>";
	h += "<tr>" +
        "<th>USER</th>" +
        "<th>WORK FUNCTION</th>" +
        "<th>LAST ACCESS</th>" +
        "<th colspan=3 class='add_user'>" +
        "<button onclick='new_user()'>Add new user</button>" +
        "</th></tr>";
	for (var i = 0; i < chefs.length; i++) {
		
		h += "<tr><td onclick='edit_user(" + chefs[i]['id'] + ");'>" +  chefs[i]['firstname'] + " " + chefs[i]['lastname'] + "</td>";
		h += "<td onclick='edit_user(" + chefs[i]['id'] + ");'>" +  sprintf('U01%06d',chefs[i]['id']); + "</td>";
		h += "<td>" + chefs[i]['function'] + "</td>";
		h += "<td>" + chefs[i]['last_login'] + "</td>";
		
		h += "<td onclick='edit_user(" + chefs[i]['id'] + ");'>edit</td>";
		// h += "<td onclick='delete_user(" + chefs[i]['id'] + ");'>del</td>";
		h += "<td onclick='conf_user_label(" + chefs[i]['id'] + ",\""+ chefs[i]['firstname'] + "\",\""+ chefs[i]['lastname'] +"\");'>Label</td>";
		h += "</tr>";
	}
	h +=  "</table>";
	div.innerHTML = h;
}

function set_user_flds(user)
{
	var flds = ['id','email','password','firstname','lastname','function'];
	for (var i = 0; i < flds.length; i++ ) {
		if (document.getElementsByName('user_' + flds[i])) {
			document.getElementsByName('user_' + flds[i])[0].value = user[flds[i]];
		}
	}
	var flds = ['admin','dock','kitchen','plating','supervisor'];
	for (var i = 0; i < flds.length; i++ ) {
		if (document.getElementsByName('user_' + flds[i])) {
			var checked = (user[flds[i]] == 1) ?true:false;
			document.getElementsByName('user_' + flds[i])[0].checked = checked;
		}
	}
}

function edit_user(id)
{
	show('show_user_div');
	center('show_user_div');
	for (var i = 0; i < chefs.length; i++) {
		if (chefs[i].id == id) {
			set_user_flds(chefs[i]);
		}
	}
}

function new_user()
{
	show('show_user_div');
	var flds = ['id','email','password','firstname','lastname','function'];
	for (var i = 0; i < flds.length; i++ ) {
		document.getElementsByName('user_' + flds[i])[0].value = '';
	}
	var flds = ['admin','dock','kitchen','plating','supervisor'];
	for (var i = 0; i < flds.length; i++ ) {
		if (document.getElementsByName('user_' + flds[i])) {
			document.getElementsByName('user_' + flds[i])[0].checked = false;
		}
	}
	
}

function save_user()
{
	var user = new Object();
	var flds = ['id','email','password','firstname','lastname','function','admin','dock','kitchen','plating','supervisor'];
	for (var i = 0; i < flds.length; i++ ) {
		user[flds[i]] = inval('user_' + flds[i]);
	}

	console.log(user);
	var data =  {data: JSON.stringify(user)};
    console.log("save_user Sent Off: %j", data);
    $.ajax({
        url: RESTHOME + "save_user.php",
        type: "POST",
        data: data,

        success: function(result) { 
        	console.log(result);
        	hide('show_user_div');
        	users();
        },
        fail: (function (result) {
            console.log("save_user fail ",result);
        })
    });
}

</script>
<div class='acs_main' id="user_table">
	<div class='popup' id='confirm_print_user_label'>
		<div class='center h2'>Print Label</div>
		<div class='center' id='ul1'>Print Label</div>
		<div class='btns'>
			<button class='button_main' onclick='print_user_label();'>Save</button>
			<button class='button_minor' onclick='hide("confirm_print_user_label");'>Cancel</button>
		</div>
	</div>
	<div class='popup' id='show_user_div'>
		<div class='user_subtitle'>Personal information</div>
		<div class='btns' id='user_div'>
				<input type='hidden' name='user_id'>
				<table>
					<tr><td><input type='text' name='user_email' placeholder="EMAIL"></td></tr>
					<tr><td><input type='text' name='user_password' placeholder="PASSWORD"></td></tr>
					<tr><td><input type='text' name='user_firstname' placeholder="FIRST NAME"></td></tr>
					<tr><td><input type='text' name='user_lastname' placeholder="LAST NAME"></td></tr>
					<tr><td><input type='text' name='user_function' placeholder="WORK POSITION"></td></tr>
				</table>
				<table>
					<tr><td>Admin</td><td><input type='checkbox' name='user_admin'></td></tr>
					<tr><td>Dock</td><td><input type='checkbox' name='user_dock'></td></tr>
					<tr><td>Kitchen</td><td><input type='checkbox' name='user_kitchen'></td></tr>
					<tr><td>Plating</td><td><input type='checkbox' name='user_plating'></td></tr>
					<tr><td>Supervisor</td><td><input type='checkbox' name='user_supervisor'></td></tr>
				</table>
		</div>
		<div class='btns'>
			<button class='button_main' onclick='save_user();'>Save</button>
			<button class='button_minor' onclick='hide("show_user_div");'>Cancel</button>
		</div>
	</div>
	<div class='acs_container'>
		<div id='users_div' class='m-10'>
		</div>
	</div>
</div>

