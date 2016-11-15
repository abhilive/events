<?php
require '../vendor/autoload.php';
require_once './config/dbHelper.php';
include_once 'objects/participants.php';
include_once 'objects/picnvideos.php';
include_once 'objects/user.php';


$app = new \Slim\Slim(array(
    'debug' => true
));

/*$app->uuid = function ($user) {
    return $user;
};*/
$app->myClosure = $app->container->protect(function ($user) {});

// For content type json to array : PUT & POST Request
/* Ref Link : http://help.slimframework.com/discussions/questions/393-interpret-json-data-with-in-app-put
*/
//$app->add(new \Slim\Middleware\ContentTypes());

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

$app->get('/', function () {
    return $res->withStatus(400)->write('Bad Request');
});

$app->get('/groups',  function () {
    global $db;
    $participants = new Participants($db);

    $response = $participants->readAllGroups();
    echoResponse(200, $response);
});

$app->get('/activities',  function () {
    global $db;
    $participants = new Participants($db);

    $response = $participants->readAllActivities();
    echoResponse(200, $response);
});

$app->get('/statuses',  function () {
    global $db;
    $participants = new Participants($db);

    $response = $participants->readAllStatuses();
    echoResponse(200, $response);
});

/*For Voting Functionality*/
$app->get('/getemails/:query_text',  function ($query_text) use ($app) {
    global $db;
    $participants = new Participants($db);

    $response = $participants->searchEmails($query_text);
    echoResponse(200, $response);
});

$app->get('/verifyuser/:email/:emp_id',  function ($email, $emp_id) use ($app) {
    global $db;
    $participants = new Participants($db);
    //$app->user = $db->getUserName($api_key);
    $response = $participants->verifyuser($email, $emp_id);
    if($response["data"]) {
        //$app->user = json_encode($response["data"]);
        session_start();
        $_SESSION["verified_user"] = $response["data"];
        //$app->container->set('foobar', function() { return 'myuser'; } );
    }
    echoResponse(200, $response);
});

$app->get('/getparticipants/:for_group',  function ($for_group) use ($app) {
    global $db;
    $participants = new Participants($db);

    $response = $participants->getparticipants($for_group);
    echoResponse(200, $response);
});

$app->post('/castvote',  function () use ($app) {
    global $db, $user;
    $participants = new Participants($db);

    $params = json_decode($app->request()->getBody());
    session_start();
    if(isset($_SESSION["verified_user"])) {
        $user = $_SESSION["verified_user"]; //Get User from session
        unset($_SESSION["verified_user"]); // Unset session after getting user data
        $params->email = $user['email'];
        $params->emp_id = $user['emp_id'];
    }
    $mandatory = array('group_id','part_id','email','emp_id');
    $response = $participants->castvote($params, $mandatory);
    echoResponse(200, $response);
});
/*End Of Code*/

$app->get('/viewparticipant/:id',  function ($id) use ($app) {
    global $db;
    $participants = new Participants($db);

    $response = $participants->load($id);
    echoResponse(200, $response);
});

$app->put('/participants/add',  function () use ($app) { 
    global $db;
    $participants = new Participants($db);

    $params = json_decode($app->request()->getBody());

    $mandatory = array('name','location','activity','description','email','status');

    $response = $participants->add($params, $mandatory);

    echoResponse(200, $response);
});

$app->put('/feedback/add',  function () use ($app) { 
    global $db;
    $participants = new Participants($db);

    $params = json_decode($app->request()->getBody());

    $mandatory = array('name','location','email','description');

    $response = $participants->addFeedback($params, $mandatory);

    echoResponse(200, $response);
});

$app->post('/login',  function () use ($app) {

    global $db;
    $user = new User($db);

    $params = json_decode($app->request->getBody());
    $mandatory = array('password','username');

    $response = $user->login($params, $mandatory);

    echoResponse(200, $response);
});

$app->post('/users',  function () use ($app) {

    global $db;
    $user = new User($db);

    $params = json_decode($app->request->getBody());
    $mandatory = array('accessToken');

    $response = $user->getUsers($params, $mandatory);

    echoResponse(200, $response);
});

$app->put('/user/add',  function () use ($app) { 
    global $db;
    $user = new User($db);

    $params = json_decode($app->request()->getBody());

    $mandatory = array('accessToken','email','emp_id','name');

    $response = $user->addUser($params, $mandatory);

    echoResponse(200, $response);
});

$app->delete('/user/:id/:access_token',  function ($id, $access_token) use ($app) { 
    global $db;
    $user = new User($db);

    $params = new StdClass();
    $params->accessToken = $access_token;
    $params->userId = $id;

    //$params = json_decode(array('accessToken'=>$access_token, 'userId'=>$id));

    $mandatory = array('accessToken','userId');
    $response = $user->deleteUser($params, $mandatory);

    echoResponse(200, $response);
});

$app->post('/logout',  function () use ($app) {

    global $db;
    $user = new User($db);
    //$response = $user->logout();
	$response = array('status'=>'success','message'=>'Logout Successful.');
    echoResponse(200, $response);
});

$app->delete('/participant/:id',  function ($id) use ($app) { 
    global $db;
    $participants = new Participants($db);

    $response = $participants->delete("participants",  array('id'=>$id));

    echoResponse(200, $response);
});

$app->put('/participant/update',  function () use ($app) { 
    global $db;
    $participants = new Participants($db);

    $params = json_decode($app->request()->getBody());

    $mandatory = array('id', 'name', 'email', 'group_id', 'location_id', 'status_id');

    $response = $participants->update($params, $mandatory);

    echoResponse(200, $response);
});

$app->get('/getparticipant/:id', function ($id) use ($app) {
    global $db;
    $participants = new Participants($db);

    $response = $participants->get($id);

    echoResponse(200, $response);
});

$app->get('/dumpPics',  function () use ($app) {
    global $db;
    $picnvideos = new Picnvideos($db);
    // query orders
    $params = $app->request->get();
    $params = (object)  $params; //Covert array to object
    $mandatory = array('location','forEvent');

    $response = $picnvideos->dumpAll($params, $mandatory);
    echoResponse(200, $response);
});

$app->post('/getpics',  function () use ($app) {
    global $db;
    $picnvideos = new Picnvideos($db);
    // query orders
    $params = json_decode($app->request->getBody());
    $mandatory = array('location','forEvent');
    
    $response = $picnvideos->getAllPics($params, $mandatory);
    echoResponse(200, $response);
});

/*For Admin User*/

$app->get('/participants', function () use ($app) {
    global $db;
    $participants = new Participants($db);

    $response = $participants->readAll();
    echoResponse(200, $response);
});

function echoResponse($status_code, $response) {
    global $app;
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response,JSON_NUMERIC_CHECK);
}

$app->run();
?>
