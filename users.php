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
	div.style.left = rect2.width / 2 + 'px';
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

	var h = "<table class='users' width='100%' border='1'>";
	h += "<tr><td>USER</td><td>WORK FUNCTION</td><td>LAST ACCESS</td><td colspan=3 class='add_user'>";
	h += "<button onclick='new_user()'>Add new user</button></td></tr>";
	for (var i = 0; i < chefs.length; i++) {
		
		h += "<tr><td>" +  chefs[i]['firstname'] + " " + chefs[i]['lastname'] + "</td>";

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
<div class='acs_main'>
	<div class='popup' id='confirm_print_user_label'>
		<div class='center h2'>Print Label</div>
		<div class='center' id='ul1'>Print Label</div>
		<div class='btns'>
			<button class='button2' onclick='print_user_label();'>OK</button>
			<button class='button2' onclick='hide("confirm_print_user_label");'>Cancel</button>
		</div>
	</div>
	<div class='popup' id='show_user_div'>
		<div class='center h2'>Edit User</div>
		<div class='center' id='user_div'>
			<div>
				<input type='hidden' name='user_id'>
				<table>
					<tr><td>Login</td><td><input type='text' name='user_email'></td></tr>
					<tr><td>Password</td><td><input type='text' name='user_password'></td></tr>
					<tr><td>First name</td><td><input type='text' name='user_firstname'></td></tr>
					<tr><td>Last Name</td><td><input type='text' name='user_lastname'></td></tr>
					<tr><td>Function</td><td><input type='text' name='user_function'></td></tr>
				</table>
			</div>
			<div>
				<table>
					<tr><td>Admin</td><td><input type='checkbox' name='user_admin'></td></tr>
					<tr><td>Dock</td><td><input type='checkbox' name='user_dock'></td></tr>
					<tr><td>Kitchen</td><td><input type='checkbox' name='user_kitchen'></td></tr>
					<tr><td>Plating</td><td><input type='checkbox' name='user_plating'></td></tr>
					<tr><td>Supervisor</td><td><input type='checkbox' name='user_supervisor'></td></tr>
				</table>
			</div>
		</div>
		<div class='btns'>
			<button class='button2' onclick='save_user();'>OK</button>
			<button class='button2' onclick='hide("show_user_div");'>Cancel</button>
		</div>
	</div>
	<div class='acs_container' id='users_div'>
	
		

<?php 
{

}
?>
</div></div>
<?php 



function delete_user()
{
	$del_id = $_GET['delete'];
	if ($del_id < 0) return;
	$sql = "delete from USERS where ID=".$del_id;
	test_mysql_query($sql);
}
function update_user()
{
	$edit_id = $_POST['edit_id'];
	if ($edit_id < 0) {
		return(new_user());
	}
	// $fieldnames = ["username","password","organisation","email","firstname","lastname"];
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from "."USERS");
	while ($row = mysql_fetch_array($result)) {

		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}

	
	$sql = "update  USERS set ";
	$n = 0;
	foreach ($fieldnames as $i => $fieldname) {
		if ($fieldname == "id") {
				
		}
		else if (substr($types[$fieldname],0,7) == "varchar" )
		{
			if ($n++ > 0) $sql .= ", ";
			$sql .= $fieldname."='".mysql_escape_string( $_POST[$fieldname])."'";
		}
		else if ($types[$fieldname] == "tinyint(1)") {
			if ($n++ > 0) $sql .= ", ";
			if (empty($_POST[$fieldname]) || $_POST[$fieldname] != 1) {
				$sql .= $fieldname."=false";
			}
			else {
				$sql .= $fieldname."=true";
			}
			
		
		}
	}

	$sql .= " where ID=".$edit_id;
	test_mysql_query($sql); 
}

function email_new_user()
{
	$bodytext = "Hi ".$_POST['firstname']." ".$_POST['familyname']."\n\n";
	
	$bodytext .= "Kind Regards.....";
	$email = new PHPMailer();
	$email->From      = 'xxx';
	$email->FromName  = 'xxx';
	$email->Subject   = 'xxxx';
	
	$email->AddAddress( $_POST['email']);
	$email->Body      = $bodytext;
	
	
	echo "sending email";
	return $email->Send();
}
function new_user()
{
	
	// $fieldnames = ["username","password","organisation","email","firstname","lastname"];
	$fieldnames = array();
	$types = array();
	$result = mysql_query("show columns from "."USERS");
	while ($row = mysql_fetch_array($result)) {

		$fieldname = $row['Field'];
		$fieldnames[] = $fieldname;
		$types[$fieldname] = $row['Type'];
	}


	$sql = "insert into USERS ";
	$flds = "(id";
	$vals = " values (null";
	$n = 0;
	foreach ($fieldnames as $i => $fieldname) {
		if ($fieldname == "id") {
			
		}
		else if (substr($types[$fieldname],0,7) == "varchar" )
		{
			if ($n > 0) {
				$flds .= ",";
				$vals .= ",";
			}
			$flds .= $fieldname;
			
			$vals .= "\"".mysql_escape_string( $_POST[$fieldname])."\"";
		}
		else if ($types[$fieldname] == "tinyint(1)") {
			if ($n > 0) {
				$flds .= ",";
				$vals .= ",";
			}
			$flds .= $fieldname;
			if (empty($_POST[$fieldname]) || $_POST[$fieldname] != 1) {
				$vals .= 'false';
			}
			else {
				$vals .= "true";
			}
		}
		$n++;
	}
	//$flds .= ",date_added)";
	//$vals .= ",now())";
	$flds .= ")";
	$vals .= ")";
	
	$sql .= $flds.$vals;
	test_mysql_query($sql);
	// email_new_user();
}
?>
