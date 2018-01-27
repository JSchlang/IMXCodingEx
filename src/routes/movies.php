<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//$app = new \Slim\App;
//$app->run();
//get all movies
$app -> get('/api/movies', function(Request $request, Response $response){
	$sql = "SELECT * FROM movies";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->query($sql);
		$movies = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($movies);;
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});

//get 1 movie by id
$app -> get('/api/movies/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');
	$sql = "SELECT * FROM movies WHERE movie_id = $id";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->query($sql);
		$movie = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($movie);
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});


//add a movie
$app -> post('/api/movies/add', function(Request $request, Response $response){
	$movie_name = $request->getParam('movie_name');
	$sql = "INSERT INTO movies (movie_name) VALUES (:movie_name)";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->prepare($sql);

		$stmt->bindParam(':movie_name', $movie_name);

		$stmt->execute();
		echo '{"success": {"text": "Movie added"}';
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});


//add a screen
$app -> post('/api/screens/add', function(Request $request, Response $response){
	$screen_type = $request->getParam('screen_type');
	$sql = "INSERT INTO screens (screen_type) VALUES (:screen_type)";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->prepare($sql);

		$stmt->bindParam(':screen_type', $screen_type);

		$stmt->execute();
		echo '{"success": {"text": "Screen added"}';
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});

// Add a time
// $app -> post('/api/times/add', function(Request $request, Response $response){
// 	$screen_type = $request->getParam('screen_type');
// 	$screen_id = $request->getParam('screen_id');
// 	$movie_name = $request->getParam('movie_name');
// 	$what_time = $request->getParam('what_time');
// 	$sql = "INSERT INTO times (what_time, screen_id, movie_id, screen_type) VALUES (:what_time, :screen_id, :movie_id, :screen_id)";
// 	try{
// 		$db = new db();
// 		$db = $db->connect();

// 		$stmt = $db->prepare($sql);

// 		$stmt->bindParam(':what_time', $what_time);
// 		$stmt->bindParam(':screen_id', $screen_id);
// 		$stmt->bindParam(':movie_name', $movie_name);
// 		$stmt->bindParam(':screen_id', $screen_id);

// 		$stmt->execute();
// 		echo '{"success": {"text": "Screen added"}';
// 	}catch(PDOException $e){
// 		echo $e->getMessage();
// 	}
// 	die();
// });

//update a movie
/*$app -> put('/api/movies/update/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');
	$movie_name = $request->getParam('movie_name');
	$sql = "UPDATE movies SET movie_name = :movie_name WHERE movie_id = $id";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->prepare($sql);

		$stmt->bindParam(':movie_name', $movie_name);

		$stmt->execute();
		echo '{"success": {"text": "Movie updated"}';
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});*/


//update a screen
$app -> put('/api/screen/update/{screen_id}', function(Request $request, Response $response){
	$screen_id = $request->getAttribute('screen_id');
	$screen_type = $request->getParam('screen_type');
	$sql = "UPDATE screens SET screen_type = :screen_type WHERE screen_id = $screen_id";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->prepare($sql);

		$stmt->bindParam(':screen_type', $screen_type);

		$stmt->execute();
		echo '{"success": {"text": "Screen updated"}';
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});



//delete a movie NOTE: ISSUE WITH DELETE DEALING WITH MY KEY SETUP
$app -> delete('/api/movies/delete/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');
	$sql = "DELETE FROM movies WHERE movie_id = $id";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$db = null;
		echo '{"success": {"text": "Movie deleted"}';
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});

//To shows a movie's time by id
$app -> get('/api/times/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');
	$sql = "SELECT movies.movie_id, movies.movie_name, screens.screen_id, screens.screen_type, times.what_time from movies, screens, times where movies.movie_id = times.movie_id AND screens.screen_id = times.screen_id AND times.movie_id = $id";
	try{
		$db = new db();
		$db = $db->connect();

		$stmt = $db->query($sql);
		$movie = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($movie);
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	die();
});

//options
$app -> get('/api/options', function(Request $request, Response $response){
		echo json_encode('{
			"imxcodingex/api/movies":"GET: displays all movies",
			"imxcodingex/api/movies/{id}":"GET: Displays the movie name of the id",
			"imxcodingex/api/movies/add":"POST: Given a json with just movie name will add the movie and increment the id",
			"imxcodingex/api/screens/add":"POST: Given a json of screen_type will add a screen and increment the id",
			"imxcodingex/api/screen/update/{screen_id}":"PUT: Given a json with screen_type will update screen type",
			"imxcodingex/api/times/{id}: GET: gets movies times by ID"
			"imxcodingex/api/options":"Displays this"
			"to test":"I used the chrome extension resteasy to test my URIs. However most of them can be tested in the app i created."
		}');
	die();
});




?>