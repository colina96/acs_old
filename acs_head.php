<?php 
session_start(); 
?>
<!DOCTYPE html>
<?php include 'db.php' ?>
<?php 
upload_menu();
function upload_menu()
{
	if(isset($_POST["submit_menu"])) {
		//	echo "menu name ".$_POST['menu_description']."<br>\n";
		//	echo "comment ".$_POST['menu_comment']."<br>\n";
		//	echo "uploaded ".$_FILES["fileToUpload"]["name"]."\n";
		//	echo "tmp name ".$_FILES["fileToUpload"]["tmp_name"]."\n";
		//	echo "start ".$_POST['menu_start']."<br>\n";
		//	echo "end ".$_POST['menu_end']."<br>\n";
		// $file = 'menu.csv';
		$file = $_FILES["fileToUpload"]["tmp_name"];

		$csv = array_map('str_getcsv', file($file));
		//	echo "read ".sizeof($csv)." rows\n";

		//	echo "read ".sizeof($csv[0])."columns\n";
		// $menu_id = new_menu("TEST MENU","description","comment");
		$sql = "insert into MENUS ";
		$flds = "(id,start_date,end_date,description,code,comment)";

		$vals = " values (null,'".$_POST['menu_start']."','".$_POST['menu_end']."',";
		$vals .= "'".mysql_escape_string( $_POST['menu_name'])."',";
		$vals .= "'".mysql_escape_string( $_POST['menu_description'])."',";
		$vals .= "'".mysql_escape_string($_POST['menu_comment'])."')";
		$sql .= $flds.$vals;
		//	echo $sql;
		test_mysql_query($sql);
		$menu_id = mysql_insert_id();
		$menu_item_id = -1;
		//echo "<table>\n";
		$ignore_next = false;
		foreach ($csv as $row)
		{
			//	echo "<tr>\n";
			if (sizeof ($row[0]) > 0) {
				$field = $row[0];
				if (is_string($field) && strlen($field) > 0) {
					if( $field[0] == 'F') {
						$ignore_next = false;
						//	echo "<td>Menu item ".$row[0]." ".$row[2]."\n";
						$sql = "insert into MENU_ITEMS ";
						$flds = "(id,menu_id,code,dish_name)";
						$vals = " values (null,".$menu_id.",";
						$vals .= "'".mysql_escape_string( $row[0])."',";
						$vals .= "'".mysql_escape_string($row[2])."')";
						$sql .= $flds.$vals;
						// echo $sql;
						test_mysql_query($sql);
							
						$menu_item_id = mysql_insert_id();
					}
					else {
						if ($row[2] == '') {
							$ignore_next = true;
						}
						if (!$ignore_next) {
							// search for component - only create new if not in already there
							$description = mysql_escape_string( $row[2]);
						//	echo "found $description";
							$sql = "select * from MENU_ITEM_COMPONENTS where description='".$description."'";
							$result = mysql_query($sql);
							$comp_id = -1;
							while ($row = mysql_fetch_array($result) ) {
								$comp_id = $row['id'];
							// insert new component
							}
							if ($comp_id == -1) {
								$sql = "insert into MENU_ITEM_COMPONENTS ";
								$flds = "(id,description)";
								$vals = " values (null,";
								$vals .= "'".$description."')";
								$sql .= $flds.$vals;
							//	echo $sql;
								test_mysql_query($sql);
								$comp_id = mysql_insert_id();
							}
							$sql = "insert into MENU_ITEM_LINK (id,menu_id,menu_item_id,component_id) ";
							$sql .= "values (null,".$menu_id.",".$menu_item_id.",".$comp_id.")";
							test_mysql_query($sql);
							// return(mysql_insert_id());
							//				add_menu_component($menu_item_id,$row[2],0);
						}
					}
				}
			}
		}


	}
}

?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Advanced Catering Services</title>

    <!-- Bootstrap -->
<link href="bootstrap.min.css" rel="stylesheet">
<script src="jquery/jquery.min.js"></script>
<script src="jquery/jquery-ui.min.js"></script -->
<link rel="stylesheet" href="jquery/jquery-ui.min.css"/>
  <!--  link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
  <!--  script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script -->

<script src="bootstrap.min.js"></script>
<!-- script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script -->

   <!-- Custom styles for this template -->
    <link href="navbar.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <?php if (!empty($_SESSION['userID'])) { ?> 
 <nav class="navbar navbar-default">
      <div class="container">
 
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#codebrainery-toggle-nav" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>

        <div class="collapse navbar-collapse" id="codebrainery-toggle-nav">
          <ul class="nav navbar-nav">
            <li class='tabclass' onclick="openPage('USERS', this, 'red','tabcontent','tabclass')"><a onclick="openPage('USERS', this, 'red')" href="#">USERS</a></li>
            <li class='tabclass' onclick="openPage('MENU', this, 'red','tabcontent','tabclass')"><a href="#">MENU</a></li>
            <li class='tabclass' onclick="openPage('SETTINGS', this, 'red','tabcontent','tabclass')"><a onclick="openPage('SETTINGS', this, 'red')" href="#">SETTINGS</a></li>
            <li class='tabclass' onclick="openPage('APP', this, 'red','tabcontent','tabclass')"><a href="#">APP</a></li>
            <li class='tabclass' onclick="openPage('REPORTS', this, 'red','tabcontent','tabclass')"><a href="#">REPORTS</a></li>
            <!--  li class='tabclass' onclick="openPage('KITCHEN', this, 'red','tabcontent','tabclass')"><a href="#">KITCHEN</a></li>
            <li class='tabclass' onclick="openPage('ASSEMBLY', this, 'red','tabcontent','tabclass')"><a href="#">ASSEMBLY</a></li -->
            
            </ul>
            <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><?php if (!empty($_SESSION['userID'])) { echo $_SESSION['user']; } ?></a></li>
            </ul>
          
        </div>

      </div> <!-- close container div -->
</nav> <!-- close navbar nav -->
<div id='main_tabs' class='container'>
<div id="USERS" class="tabcontent">
  <?php include 'users.php' ?>
</div>

<div id="MENU" class="tabcontent">
  <?php include 'menu.php' ?>
</div>

<div id="SETTINGS" class="tabcontent">
  <h3>Settings</h3>
  <?php include 'acs_settings.php' ?>
</div>

<div id="APP" class="tabcontent">
<div class='phone'>
  <iframe width='360px' height='616px' src='app/www/index.html' class='app_frame'></iframe>
</div>
</div>
<div id="REPORTS" class="tabcontent">
coming soon.....
  
</div>
<div id="KITCHEN" class="tabcontent">
  <?php include 'kitchen.php' ?>
</div>
<div id="ASSEMBLY" class="tabcontent">
  <?php include 'assembly.php' ?>
</div>
</div>
<?php } else { ?>


<div class="modal fade">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <!--  button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button -->
          <h2 class="form-signin-heading">Please sign in</h2>
        </div>
        <div class="acs_container">

      		<form class="form-signin" method="post">
        
        	<label for="inputEmail" class="sr-only">Email address</label>
        	<input id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
        	<label for="inputPassword" class="sr-only">Password</label>
        	<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        	<button class="btn btn-lg btn-primary btn-block" type="submit" name="login" value='1'>Sign in</button>
      		</form>

    	</div> <!-- /container -->
      </div>
    </div>
  </div>
 <?php } ?> 
<?php 
	if (empty($_SESSION['userID'])) {
?>
<script src="app.js"></script>
<?php } ?>
<script src="acs.js"></script>
