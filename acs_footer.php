<script>
var comps = null;
var menu_items = null;
var preptypes = null;
function check_login()
{
var loginString ="";
console.log("checking login");

$.ajax({
    type: "POST",crossDomain: true, cache: false,
    url:  RESTHOME + "login.php",
    data: loginString,
    dataType: 'json',
    success: function(data){
    	console.log("got login");
    	console.log(data);
    	if (data.user_id == -1) window.location.reload();
 
    }
});
}

function check_login_timer()
{
	// console.log("time!");
	// load_tracking_data();
	check_login();
	setTimeout(check_login_timer,60 * 1000);
}
$(document).ready(function(){
	
	load_comps();
	load_preptypes();
	load_menu_items(0);

<?php 
	if (empty($_SESSION['userID'])) {
?>
	$('.modal').modal('show');
<?php }
else {
?>
	check_login_timer();
<?php 
}
?>
	  
	//Get the element with id="defaultOpen" and click on it
	console.log("doc ready open page ",default_tab);
	show_active_menus();
	openPage(default_tab, this, 'red','tabcontent','tabclass');
	});
$( function() { $( "#menu_start" ).datepicker({ dateFormat: 'yy-mm-dd' });} );
$( function() { $( "#menu_end" ).datepicker({ dateFormat: 'yy-mm-dd' });} );
$( function() { $( "#report_start" ).datepicker({ dateFormat: 'yy-mm-dd' });} );
$( function() { $( "#report_end" ).datepicker({ dateFormat: 'yy-mm-dd' });} );
	
$(".datepicker").attr("autocomplete", "off");
</script> 
<div class="container">

</div>
  </body>
</html>

