<script>
var comps = null;
var preptypes = null;
$(document).ready(function(){
	load_comps();
	load_preptypes();
	comps = ["ActionScript","AppleScript","Asp","BASIC","C","C++",
	                       "Clojure","COBOL","ColdFusion","Erlang","Fortran","Groovy","Haskell",
	                       "Java","JavaScript","Lisp","Perl","PHP","Python","Ruby","Scala","Scheme"];
	
	

<?php 
	if (empty($_SESSION['userID'])) {
?>
	$('.modal').modal('show');
<?php } ?>
	  
	//Get the element with id="defaultOpen" and click on it
	console.log("doc ready open page ",default_tab);
	openPage(default_tab, this, 'red','tabcontent','tabclass');
	});
// var comps = null;
function get_preptype_val(id,fld)
{
	for (var i = 0; i < preptypes.length; i++) {
		if (preptypes[i].id == id) {
			return(preptypes[i][fld]);
		}
	}
	return("not found");
}
function load_preptypes()
{
console.log("loading prep types");
    $.ajax({
        url: "REST/get_preptypes.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            preptypes = result;
            
            console.log("got " + result.length + " preptypes");
            
        },
        done: function(result) {
            console.log("done preptypes ");
        },
        fail: (function (result) {
            console.log("fail preptypes",result);
        })
    });
}
function load_comps()
{
console.log("loading menu item components");
    $.ajax({
        url: "REST/get_comps.php",
        type: "POST",
       // data: data,
       //  data: {points: JSON.stringify(points)},
        dataType: 'json',
        // contentType: "application/json",
        success: function(result) {
            comps = result;
            $('#search').autocomplete({
                // This shows the min length of charcters that must be typed before the autocomplete looks for a match.
                minLength: 2,
        		source: comps
                    
            })
            console.log("got " + result.length + " comps");
            
        },
        done: function(result) {
            console.log("done load_comps ");
        },
        fail: (function (result) {
            console.log("fail load_comps",result);
        })
    });
}

</script> 
<div class="container">

</div>
  </body>
</html>

