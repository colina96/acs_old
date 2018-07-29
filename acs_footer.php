<script>
var comps = null;
var menu_items = null;
var preptypes = null;
$(document).ready(function(){
	load_comps();
	load_preptypes();
	load_menu_items(0);

<?php 
	if (empty($_SESSION['userID'])) {
?>
	$('.modal').modal('show');
<?php } ?>
	  
	//Get the element with id="defaultOpen" and click on it
	console.log("doc ready open page ",default_tab);
	openPage(default_tab, this, 'red','tabcontent','tabclass');
	});

	$( function() { $( "#menu_start" ).datepicker({ dateFormat: 'yy-mm-dd' });} );
	$( function() { $( "#menu_end" ).datepicker({ dateFormat: 'yy-mm-dd' });} );

</script> 
<div class="container">

</div>
  </body>
</html>

