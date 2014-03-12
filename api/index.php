<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');  //On or Off

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/wines', 'getWines');
$app->get('/wines/:id', 'getWine');
/*
$app->get('/wines/search/:query', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines:id', 'updateWine');
$app->delete('/wines/:id', 'deleteWine');

*/

$app->run();

function getWines() {
    $sql = "select * from wine order by name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($wines);
    } catch (PDOException $e) {
        echo '{"error" : {"text" :'. $e->getMessage() .'}}';
    }
}

function getWine($id) {
    $sql = "select * from wine where id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $wine = $stmt->fetchObject();
        $db = null;
        echo json_encode($wine);
    } catch (PDOException $e) {
        echo '{"error" : {"text" : ' . $e->getMessage() .'}}';
    }
}

function addWine() {
    error_log('addWine\n', 3, '/var/tmp/php.log');
    $request = Slim::getInstance()->request();
    $wine = json_decode($request->getBody());
    $sql = "insert into wine(name, grapes, country, region, year, description) values( :name, :graphes, :country, :region, :year, :description)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('name', $wine->name);
        $stmt->bindParam('graphes' $wine->grapes);
        $stmt->bindParam('country', $wine->country);
        $stmt->bindParam('region', $wine->region);
        $stmt->bindParam('year', $wine->year);
        $stmt->bindParam('description', $wine->description);
        $stmt->execute();
        $wine->id = $db->lastInsertId();
        $db = null;
        echo json_encode($wine);
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error" : {"text" : ' . $e->getMessage() .'}}';
    }

}

function updateWine($id) {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $wine = json_decode($body);
    $sql = "update wine set name=:name, graphes= :graphes, country= :country, region= :region, year= :year, description= :description where id= :id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $wine->name);
        $stmt->bindParam("graphes", $wine->graphes);
        $stmt->bindParam("country", $wine->country);
        $stmt->bindParam("region", $wine->region);
        $stmt->bindParam("year", $wine->year);
        $stmt->bindParam("description", $wine->description);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($wine);

    } catch (PDOException $e) {
           echo '{"error" : {"text" : ' . $e->getMessage() .'}}';
    }
}

function deleteWine($id) {
    $sql = "delete from wine where id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
    } catch (PDOException $e) {
           echo '{"error" : {"text" : ' . $e->getMessage() .'}}';
    }
}

function findByName($query) {
    $sql = "select * from wine where upper(name) like :query by name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"wine" : ' .json_encode($wines) .'}';

    } catch (PDOException $e) {
           echo '{"error" : {"text" : ' . $e->getMessage() .'}}';
    }
}

function getConnection() {
    $dbhost = "localhost";
    $dbuser = 'root';
    $dbpass = 'root';
    $dbname = 'cellar';
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}