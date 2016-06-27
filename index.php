<?php
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");         

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}

	require 'Slim/Slim.php';
	require_once('vendor/bshaffer/oauth2-server-php/src/OAuth2/Autoloader.php');

	\Slim\Slim::registerAutoloader();
	OAuth2\Autoloader::register();

	$application = new \Slim\Slim();

	$application->get('/pics/get', 'all');					/* GET */
	$application->get('/pics/get/:id', 'get');				/* GET :id */
	$application->post('/pics/post', 'post');				/* CREATE */
	$application->put('/pics/put/:id', 'put');				/* UPDATE :id */
	$application->delete('/pics/delete/:id', 'delete');	/* DELETE :id */
	$application->get('/server', 'server');	/* SERVER */

	$application->run();

	/* DATABASE */
	function getDB() {
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpass = "";
		$dbname = "fhaare";

		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
	}

	function server() {
		echo json_encode($_SERVER);
	}

	/* GET */
	function all() {
        $db = getDB();
 	 	$app = \Slim\Slim::getInstance();

 	 	$tab = $app->request->headers;
		$table = 'pics'; //$tab['table'];

 	 	try {
            $sth = $db->prepare("SELECT * FROM $table");
            $sth->execute();
            $res = $sth->fetchAll(PDO::FETCH_OBJ);

            if($res){
	        	$app->response->setStatus(200);
	        	$app->response()->headers->set('Content-Type', 'application/json');
	            echo json_encode($res);
	            $db = null;
            } else {
            	throw new PDOException('No records found.');
            }
        } catch (PDOException $e) {
        	$app->response->setStatus(404);
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
	}

	/* GET :id */
	function get($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->headers;
		$table = 'pics';//$tab['table'];

		try {
			$sth = $db->prepare("SELECT * FROM $table WHERE id = :id");
			$sth->execute(array(
					'id'   	=> (int)$id
				));
			$res = $sth->fetch(PDO::FETCH_OBJ);

			if($res) {
				$app->response->setStatus(200);
				$app->response()->headers->set('Content-Type', 'application/json');
				echo json_encode($res);
				$db = null;
			} else {
				throw new PDOException('No records found.');
			}
		} catch (PDOException $e) {
			$app->response->setStatus(404);
			echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

	/* CREATE */
	function post() {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->post();

		$table = 'pics';//$tab['table'];
		unset($tab['_METHOD']);
		unset($tab['table']);

		$keys = implode(',', array_keys($tab));
        $values = implode(',', array_values($tab));;

		$countKey = count($tab);

        $int = array();
        for ($i=0; $i < $countKey; $i++) {
            array_push($int, '?');
        }

        $interrogation = implode(',', array_values($int));

        try {
			if($countKey === 0){
				exit(0);
			} else {
				$insert = $db->prepare("INSERT INTO $table ($keys) VALUES($interrogation)");
				$insert->execute(array_values($tab));
			}
			$app->response->setStatus(201);
			$app->response()->headers->set('Content-Type', 'application/json');
			echo json_encode(array(
					"status" => "success", 
					"code" => 1
				));
			$db = null;

		} catch(PDOException $e) {
			$app->response->setStatus(404);
			echo json_encode(array(
					"error" => $e->getMessage(), 
					"code" => 2
				));
		}
	}

	/* UPDATE :id */
	function put($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->put();

		$table = 'pics';//$tab['table'];
		unset($tab['_METHOD']);
		unset($tab['table']);

        $setter = [];
        foreach ($tab as $key => $value) {
        	array_push($setter, $key .' = :'.$key);
        }
        $keys = implode(', ', $setter);

		try {
			$sth = $db->prepare("UPDATE $table SET $keys WHERE id = $id");
			$sth->execute($tab);

			$app->response->setStatus(200);
			$app->response()->headers->set('Content-Type', 'application/json');
			echo json_encode(array(
					"status" => "success",
					"code" => 1
				));
			$db = null;
		
		} catch(PDOException $e) {
			$app->response->setStatus(404);
			echo json_encode(array(
					"error" => $e->getMessage(), 
					"code" => 2
				));
		}
	}

	/* DELETE :id */
	function delete($id) {
 		$db = getDB();
		$app = \Slim\Slim::getInstance();
		$tab = $app->request->delete();
		$table = 'pics';//$tab['table'];

 		try {
			$sth = $db->prepare("SELECT * FROM $table WHERE id = :id");
			$sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();

            $res = $sth->fetch(PDO::FETCH_OBJ);

			if($res) {
				$sth = $db->prepare("DELETE FROM $table WHERE id = :id");
				$sth->bindParam(':id', $id, PDO::PARAM_INT);
				$sth->execute();

				$app->response->setStatus(204);
			} else {
				throw new PDOException('No records found.');
			}
		} catch (PDOException $e) {
			$app->response->setStatus(400);
			$app->response()->headers->set('X-Status-Reason', 'application/json');
			echo json_encode(array(
					"error" => $e->getMessage(), 
					"code" => 2
				));
		}
	}
?>