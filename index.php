<?php include 'acs_head.php' ?>
<script>
var default_tab = 'SETTINGS'; 

$(document).ready(function(){
<?php 
	if (empty($_SESSION['userID'])) {
?>
	$('.modal').modal('show');
<?php } ?>
	  
	//Get the element with id="defaultOpen" and click on it
	  document.getElementById(default_tab).click();
	});
</script> 
<div class="container">

</div>
  </body>
</html>
