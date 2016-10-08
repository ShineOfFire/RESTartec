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

	$application->get('/server', 'server');										/* SERVER */

	$application->get('/users/get', 'allUsers');								/* GET */
	$application->get('/users/get/:id', 'getUsers');							/* GET :id */
	$application->post('/users/post', 'postUsers');								/* CREATE */
	$application->put('/users/put/:id', 'putUsers');							/* UPDATE :id */
	$application->delete('/users/delete/:id', 'deleteUsers');					/* DELETE :id */

	$application->get('/livemaps/get', 'allLiveMaps');							/* GET */
	$application->get('/livemaps/get/:id', 'getLiveMaps');						/* GET :id */
	$application->post('/livemaps/post', 'postLiveMaps');						/* CREATE */
	$application->put('/livemaps/put/:id', 'putLiveMaps');						/* UPDATE :id */
	$application->delete('/livemaps/delete/:id', 'deleteLiveMaps');				/* DELETE :id */

	$application->get('/clientSearch/get', 'allClientSearch');					/* GET */
	$application->get('/clientSearch/get/:id', 'getClientSearch');				/* GET :id */
	$application->post('/clientSearch/post', 'postClientSearch');				/* CREATE */
	$application->put('/clientSearch/put/:id', 'putClientSearch');				/* UPDATE :id */
	$application->delete('/clientSearch/delete/:id', 'deleteClientSearch');		/* DELETE :id */

	/* CUSTOM FEATURE */
	$application->delete('/livemaps/remove', 'remove');		    		/* REMOVE ALL OLD USERS */

	$application->run();

	/* DATABASE */
	function getDB() {

    	//webartec_server
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpass = "";
		$dbname = "igo";

		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
	}

	function server() {
		echo json_encode($_SERVER);
	}

	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------- USERS ----------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/

	/* GET */
	function allUsers() {
        $db = getDB();
 	 	$app = \Slim\Slim::getInstance();

 	 	$tab = $app->request->headers;
		$table = 'users'; //$tab['table'];

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
	function getUsers($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->headers;
		$table = 'users';//$tab['table'];

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
	function postUsers() {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->post();

		$table = 'users';//$tab['table'];
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
	function putUsers($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->put();

		$table = 'users';//$tab['table'];
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
	function deleteUsers($id) {
 		$db = getDB();
		$app = \Slim\Slim::getInstance();
		$tab = $app->request->delete();
		$table = 'users';//$tab['table'];

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


	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*----------------------------------- LIVEMAPS ----------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/



	/* GET */
	function allLiveMaps() {
        $db = getDB();
 	 	$app = \Slim\Slim::getInstance();

 	 	$tab = $app->request->headers;
		$table = 'livemaps'; //$tab['table'];

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
	function getLiveMaps($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->headers;
		$table = 'livemaps';//$tab['table'];

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
	function postLiveMaps() {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->post();

		$table = 'livemaps';//$tab['table'];
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
	function putLiveMaps($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->put();

		$table = 'livemaps';//$tab['table'];
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
	function deleteLiveMaps($id) {
 		$db = getDB();
		$app = \Slim\Slim::getInstance();
		$tab = $app->request->delete();
		$table = 'livemaps';//$tab['table'];

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

	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*--------------------------------- CLIENTSEARCH --------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/

	/* GET */
	function allClientSearch() {
        $db = getDB();
 	 	$app = \Slim\Slim::getInstance();

 	 	$tab = $app->request->headers;
		$table = 'clientSearch'; //$tab['table'];

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
	function getClientSearch($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->headers;
		$table = 'clientSearch';//$tab['table'];

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
	function postClientSearch() {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->post();

		$table = 'clientSearch';//$tab['table'];
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
	function putClientSearch($id) {
		$db = getDB();
		$app = \Slim\Slim::getInstance();

		$tab = $app->request->put();

		$table = 'clientSearch';//$tab['table'];
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
	function deleteClientSearch($id) {
 		$db = getDB();
		$app = \Slim\Slim::getInstance();
		$tab = $app->request->delete();
		$table = 'clientSearch';//$tab['table'];

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

	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------- CUSTOM FEATURE -------------------------------*/
	/*-------------------------------------------------------------------------------*/
	/*-------------------------------------------------------------------------------*/

	/* CUSTOM FEATURE */
	function remove() {
		$db = getDB();
		$app = \Slim\Slim::getInstance();
		$tab = $app->request->delete();
		$table = 'livemaps';//$tab['table'];

		// On supprime les membres qui ne sont pas sur la page donc qui n'ont pas actualisé automatiquement cet fonction
 		try {
			$sth = $db->prepare("DELETE FROM $table WHERE timestamp < :timenow");
			$sth->execute(array(
				'timenow' => time()-20
			));

			$app->response->setStatus(204);
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