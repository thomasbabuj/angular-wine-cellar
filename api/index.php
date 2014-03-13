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

function getConnection() {
    $dbhost = "localhost";
    $dbuser = 'root';
    $dbpass = 'root';
    $dbname = 'cellar';
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}