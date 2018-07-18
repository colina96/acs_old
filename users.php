<?php 
#require('PHPMailer/PHPMailer.php');
#require('PHPMailer/Exception.php');

#use PHPMailer\PHPMailer\PHPMailer;
?>
<div class='acs_main'>
<div class='acs_container'>
<?php 
{
	if( !empty($_SESSION['userID'] && $_SESSION['admin'] == 1)) { // logged in
		if (!empty($_GET['delete'])) { delete_user(); }
		if (!empty($_POST['update'])) { update_user(); }
		if (!empty($_GET['edit'])) {
			
			show_user($_GET['edit']);
			echo "<div class='draw_screen_btn'><a class='users_link' href='acs_users.php'>Back</a></div>";
		}
		else if (!empty($_GET['new'])) {
			show_user(-1);
			echo "<div class='draw_screen_btn'><a class='users_link' href='acs_users.php'>Back</a></div>";
		}
		else {
			show_users();
			// echo "<div class='draw_screen_btn'><a class='users_link' href='acs_users.php?new=1'>New User</a></div>";
		}
	}
}
?>
</div></div>
<?php 

function show_users()
{
	$sql = "select * from USERS order by ID";
	$result = mysql_query($sql);
	if ($result) {

		echo "<table class='users' width='100%' border='1'>";
		echo "<tr><td>USER</td><td>WORK FUNCTION</td><td>LAST ACCESS</td><td colspan=2 class='add_user'>";
		echo "<a class='users_link' href='acs_users.php?new=1'>Add new user</a></td></tr>";
		while($row = mysql_fetch_array($result))
		{
//			echo "<tr class='users'><td><a href='?edit=".$row['id']."'>".$row['id']."</a></td>";
//			echo "<td>".$row['username']."</td><td>".$row['password']."</td>";
			echo "<td>".$row['firstname']." ".$row['lastname']."</td>";
			echo "<td>".$row['function']."</td>";
			echo "<td>".$row['last_login']."</td>";
			echo "<td><a href='acs_users.php?edit=".$row['id']."'>edit</a></td>";
			echo "<td><A href='acs_users.php?delete=".$row['id']."'>del</a></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}

function show_user($edit_id)
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
	if ($edit_id >= 0) {
		$sql = "select * from USERS where ID=".$edit_id;
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
	}
	if ($result || $edit_id == -1) {

		echo "<form method='POST' action='acs_users.php'><table class='users' width='100%'>";
	//	while ($row = mysql_fetch_array($result) )
		{
			// echo "<tr><td>".$row['id']."</td>";
			foreach ($fieldnames as $i => $fieldname) {
				if ($fieldname == "id") {
					
				} 
				else if (substr($types[$fieldname],0,7) == "varchar" )
				{
					echo "<tr><td>".$fieldname."</td><td class='length_inputs'>";
					echo "<input name='".$fieldname."' value=\"".$row[$fieldname]."\" width='100%'></td></tr>";
				}
			}
		}
		echo "</table>";
		echo "<input type=hidden name='edit_id' value='".$edit_id."'>";
		echo "<input type='submit' name='update' value='update' class='draw_screen_btn users_submit'></form>";
	}
}

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
	}

	$sql .= " where ID=".$edit_id;
	test_mysql_query($sql); 
}

function email_new_user()
{
	$link = "http://$_SERVER[HTTP_HOST]/custom-flashing/index.php?login=1&username=".$_POST['username']."&password=".$_POST['password'];
	$bodytext = "Hi ".$_POST['firstname']." ".$_POST['familyname']."\n\n";
	$bodytext .= "Welcome to the Roofit online flashing designer\n\n";
	$bodytext .= "You can access the system by clicking <a href='".$link."'>here</a>\n";
	$bodytext .= "or by using this link\n\n".$link."\n\n";
	$bodytext .= "Kind Regards.....";
	$email = new PHPMailer();
	$email->From      = 'admin@roofit.online';
	$email->FromName  = 'roofit';
	$email->Subject   = 'Roofit flashing design';
	
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
