<?php
require '../vendor/autoload.php';
require_once './config/dbHelper.php';
include_once 'objects/participants.php';
include_once 'objects/picnvideos.php';
include_once 'objects/user.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);

use \Slim\Http\Request;
use \Slim\Http\Response;

$app = new \Slim\App($c);

$dbHelper = new dbHelper();
$db = $dbHelper->getConnection();

/**
 * Database Helper Function templates
 */
/*
select(table name, where clause as associative array)
insert(table name, data as associative array, mandatory column names as array)
update(table name, column names as associative array, where clause as associative array, required columns as array)
delete(table name, where clause as array)
*/

$app->get('/', function (Request $req,  Response $res, $args = []) {
    return $res->withStatus(400)->write('Bad Request');
});

$app->get('/groups',  function (Request $req,  Response $res, $args = []) {
    global $db;
    $participants = new Participants($db);
    // query orders

    $response = $participants->readAllGroups();
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->get('/activities',  function (Request $req,  Response $res, $args = []) {
    global $db;
    $participants = new Participants($db);
    // query orders

    $response = $participants->readAllActivities();
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->get('/participants',  function (Request $req,  Response $res, $args = []) {
    global $db;
    $participants = new Participants($db);
    // query orders

    $response = $participants->readAll();
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

// Orders
$app->get('/orders', function() {
    global $db;
    // initialize object
    $order = new Order($db);
    // query orders
    $data = $order->readAll();
    echoResponse(200, $data);
});

// Export-Orders
$app->post('/login',  function (Request $req,  Response $res, $args = []) {

    global $db;
    $user = new User($db);
    $response = $user->login($req->getParams());

    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->post('/logout',  function (Request $req,  Response $res, $args = []) {

    global $db;
    $user = new User($db);
    //$response = $user->logout();
	$response = array('status'=>'success','message'=>'Logout Successful.');
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->delete('/participants/{id}',  function (Request $req,  Response $res, $args = []) { 

    global $db;
    $participants = new Participants($db);

    $response = $participants->delete("participants",  $args);

    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->post('/participants/add',  function (Request $req,  Response $res, $args = []) { 

    global $db;
    $participants = new Participants($db);
    $response = $participants->add($req->getParams());

    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->get('/dumpPics',  function (Request $req,  Response $res, $args = []) {
    global $db;
    $picnvideos = new Picnvideos($db);
    // query orders

    $response = $picnvideos->dumpAll();
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

$app->put('/getpics',  function (Request $req,  Response $res, $args = []) {
    global $db;
    $picnvideos = new Picnvideos($db);
    // query orders
    $response = $picnvideos->getAllPics($req->getParams());
    return $res->withStatus(200, 'OK')
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($response,JSON_NUMERIC_CHECK));
});

// Products
/*$app->get('/products', function() {
    global $db;
    // initialize object
    $product = new Product($db);
    // query products
    $data = $product->readAll();
    echoResponse(200, $data);
});*/
/* Deprecated - Below Twos
$app->post('/orders', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('issuedTo','issuedBy');
    global $db;
    $order = new Order($db);
    $rows = $order->create("orders", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Information saved successfully.";
    echoResponse(200, $rows);
});

// Export-Orders
$app->post('/validateno', function() use ($app) {
    global $db;
    $recharge = new Recharge($db);
    $data = json_decode($app->request->getBody());
    //$mandatory = array('exportedBy');
    // query orders to export
    $rows = $recharge->validateNo($data);
    if($rows["status"]=="success")
        $rows["message"] = 'File generated successfully.';
    echoResponse(200, $rows);
});
*/
$app->run();
?>
