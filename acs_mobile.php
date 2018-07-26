
<!DOCTYPE html>
<?php
session_start();
?>
<?php include 'db.php' ?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Advanced Catering Services</title>
<script src="jquery/jquery.min.js"></script>
<script src="jquery/jquery-ui.min.js"></script -->
<link rel="stylesheet" href="jquery/jquery-ui.min.css"/>
<script src="acs.js"></script>
    <!-- Bootstrap -->
<!--  >link href="bootstrap.min.css" rel="stylesheet">
<script src="jquery.min.js"></script>
<script src="bootstrap.min.js"></script -->


   <!-- Custom styles for this template -->
    <link href="acs_mobile.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
  <?php 
	if (empty($_SESSION['userID'])) {
?>
	<div class='mobile_main' id='login_page'>
	<div class='login_head'>Login</div>
	<form method='GET'>
	<input class='input' name='email' placeholder='email'>
	<input name='password' type='password' placeholder='password'>
	<input type='submit' name='login' value='Login'>
	</form>
	</div>
<?php 
	}
	else {
?>
	<div class='mobile_main mobile_main_active' id='mm1'>
		<div class='m_main'>
		<div class='m_login'><?php if (!empty($_SESSION['userID'])) { echo $_SESSION['user']; } ?></div>
		<button type='button'  class='m_btn' onclick="openPage('mm2', this, 'red','mobile_main','tabclass');">Continue</button>
		</div>
	</div>
	<div class='mobile_main hidden' id='mm2'>
		<div class='m_main'>
		<div class='m_top_menu_container'>
			<div class='m_top_menu' id='m_current_tracking_tab'  onclick="m_tracking()">CURRENT TRACKING</div>
			<div class='m_top_menu' id='m_reprint_labels_tab'  onclick="openPage('m_reprint_labels', this, 'red','m_modal','tabclass');">RE-PRINT LABELS</div>
			<div class='m_top_menu' id='m_search_tab'  onclick="openPage('m_search', this, 'red','m_modal','tabclass');">SEARCH</div>
		</div>
		<div class='m_modal' id='m_current_tracking' >CURRENT TRACKING</div>
		<div class='m_modal hidden' id='m_reprint_labels'>RE-PRINT LABELS</div>
		<div class='m_modal hidden' id='m_search'>SEARCH</div>
		<!--   button type='button'  class='m_btn' onclick='m_continue1();'>Continue</button -->
		</div>
	</div>
<?php 
	}
?>
  </div>
<script>
function m_tracking()
{
	console.log('goto_active_components');
	openPage('m_current_tracking', this, 'red','m_modal','tabclass');
	document.getElementById('m_current_tracking').innerHTML = "loading....";
	 $.ajax({
	        url: "REST/get_active_comps.php",
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
</script>
  <?php include 'acs_footer.php' ?>