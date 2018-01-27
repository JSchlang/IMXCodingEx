<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    //$name = $args['name'];
    //$response->getBody()->write("Hello, $name");
    //return $response;
});



//Movie routes
require '../src/routes/movies.php';

$app->run();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Movies Times</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
  <script src= "https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src= "https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
  <script src= "https://cdn.datatables.net/select/1.2.4/js/dataTables.select.min.js"></script>
</head>
<body>

<div class="container-fluid" id = "table1">
<table id="movies2" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead><tr>
		<th>#</th>
		<th>Movie</th>
	</tr></thead>
</table>
<button id= "add" type="button" class="btn btn-primary">Add</button>
</div>

<div classs = "container-fluid" id = "table2">
<table id="movies3" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead><tr>
		<th>#</th>
		<th>Movie</th>
		<th>Screen Number</th>
		<th>Type</th>
		<th>Time</th>
	</tr></thead>
</table>
	<button id= "go_back" type="button" class="btn btn-primary">Back</button>
	<button id= "add2" type="button" class="btn btn-primary">Add</button>
</div>

<div classs = "container-fluid" id = "form_add">
<form  name = "theForm" method="POST">
  <div class="form-group">
    <label for="form_madd">Movie name</label>
    <input type="text" class="form-control" id="form_madd" aria-describedby="movie_note" placeholder="Enter movie name">
    <small id="movie_note" class="form-text text-muted">Max 30 characters</small>
  </div>
  <div class="form-group">
    <label for="form_screennum">Screen number</label>
    <input type="text" class="form-control" id="form_screennum" placeholder="Leave blank if new screen">
  </div>
  <div class="form-group">
    <label for="form_screenfmat">Movie format</label>
    <input type="text" class="form-control" id="form_screenfmat" placeholder="Standard, xd, imax, etc..">
  </div>
    <div class="form-group">
    <label for="form_time">Movie time</label>
    <input type="text" class="form-control" id="form_time" placeholder="hh:mm:ss">
  </div>
  <button type="button" class="btn btn-primary" id="form_submit">Submit</button>
</form>
</div>

  <script id="source" language="javascript" type="text/javascript">
  	//initial table of movie id and names
	$(document).ready(function() {
		var toHide = document.getElementById("table2");
		toHide.style.display = "none";
		toHide = document.getElementById("form_add");
		toHide.style.display = "none";
    	$('#movies2').DataTable({
    		select: true,
        	"processing" : true,
        	"ajax" : {
            	"url" : "/api/movies",
            	dataSrc : ''
        	},
        	"columns" : [ 
            	{"data" : "movie_id"},
        		{"data" : "movie_name"}      	
        	]
    	});
	

	//onclick table of selected movie's showtimes and screens
	$('#movies2 tbody').on('click', 'tr', function () {
		var toHide = document.getElementById("table1");
		toHide.style.display = "none";
		toHide = document.getElementById("table2");
		toHide.style.display = "block";
		var table = $('#movies2').DataTable();
    	var id = table.row(this).data().movie_id;
    	console.log( 'You clicked on '+id+'\'s row' );
    	console.log("/api/times/"+id);
    	$('#movies3').DataTable({
		select: true,
    	"processing" : true,
    	"ajax" : {
        	"url" : "/api/times/"+id,
        	dataSrc : ''
    	},
    	"columns" : [ 
        	{"data" : "movie_id"},
    		{"data" : "movie_name"},
    		{"data" : "screen_id"},
    		{"data" : "screen_type"},
    		{"data" : "what_time"},       	
    	]
		});
	} );

	//go back button reloads the page
	$('#go_back').on('click', function(){
		console.log("clicked go back");
		location.reload();
	});

	//submit form_add 
	$('#form_submit').on('click', function(){
		console.log("submit clicked");
		var movie_name = document.getElementById('form_madd');
		movie_name = movie_name.value;
		if(movie_name != ""){
			$.ajax({
				type: 'POST',
				url: "/api/movies/add",
				data: {"movie_name": movie_name}
			});
		}

		var screen_type = document.getElementById('form_screenfmat');
		var screen_type = screen_type.value;
		var screen_id = document.getElementById('form_screennum');
		var screen_id = screen_id.value;
		//There is no screen id so this is a new screen.
		if (screen_id == ""){
			if(screen_type != ""){ //There a format. So add the screen.
				$.ajax({
					type: 'POST',
					url: "/api/screens/add",
					data: {"screen_type": screen_type}
				});
			}
		}else{
			$.ajax({
				type: 'PUT',
				url: "/api/screen/update/" + screen_id,
				data: {"screen_type": screen_type}
			});
		}

		//if I want to add just a time there should exist a movie and a screen
		//OR both have just been added
		var what_time = document.getElementById('form_time');
		var what_time = what_time.value;
		console.log(what_time);
		if(movie_name != ""){
			$.ajax({
				type: 'POST',
				url: "/api/times/add",
				data: {"screen_type": screen_type, 
					   "screen_id":screen_id,
					   "movie_name": movie_name,
					   "what_time":what_time,
					   "movie_name":movie_name
					  }
			});
		}


		console.log(movie_name, screen_id, screen_type, what_time);


	});

	//detect add button clicks and call hide_form()
	$('#add').on('click', function(){
		console.log("clicked add");
		hideForm();
	});

	$('#add2').on('click', function(){
		console.log("clicked add");
		hideForm();	
	});

	//hides the form
	function hideForm(){
				//location.reload();
		toHide = document.getElementById("form_add");
		if(toHide.style.display === "none"){
			toHide.style.display = "block";
		}
		else{
			toHide.style.display = "none";
		}
	}





	});

</script>

</body>
</html>