<?php

// First we include the restserver lib
//include '../restserver.phar';
include '../Rest/Server.php';


// Them we instantiate the server
// Using the rewrite rule on .htaccess file the request url is passed into $_GET['q']
// Therefore we pass this parameter to restserver
// If app is running on root and .htacces working, them restserver can guess the url
$server = new Rest\Server($_GET["q"]);


// The server handles proper mime-types, * is for no extension
$server->setAccept(array("*","application/json"));

// Using the "setParameter" we can have some globaly available vars
// This vars will be accessible to all requests handlers
// Here I define a salt for the user authentication
$server->setParameter("salt","&dcalsd-09o");

// Since this is just a demo I don't want to get much into
// Logic and stuff, so I'm creating a database on sqlite on
// the fly just to run something
$create = (!file_Exists("data.db"));
if($create) touch("data.db");
$db = new PDO("sqlite:data.db");
if($create) $db->query(file_get_contents("schema.sql"));

// Again using the server to setup some global database
$server->setParameter("db",$db);

// This is a dummy authentication function receiving 
// a server instance (exactly like the handlers will receive)
function authenticate($server) {
    // First we recover the global parameters
    $db = $server->getParameter("db"); // the database connection
    $salt = $server->getParameter("salt"); // the choosen salt

    // The server authenticator is used for HTTP BASIC authentication
    // You can access it using $server->getAuthenticator()
    $login = $server->getAuthenticator()->getUser();
    $pass  = $server->getAuthenticator()->getPassword();

    // Now we query the database to check if supplied information is valid
    $auth = $db->prepare("select password from users where login = ?");
    $auth->execute(array($login));
    if(($a = $auth->fetchColumn(0)) != md5($salt.$pass)) {
        return false;
    } else {
        return true;
    }
}

// This a url mapping function, the core of the RestServer
// You pass in a HTTP Method (like GET, POST, DELETE...) 
// A URL to map (this case is root "/") and a handler
// The handler can be a closure receiving the server instance
// Or your own Controller that implements RestController
$server->addMap("GET","/",function($server) {
    // This class will redirect to index.html
    return new Rest\Controller\Redirect("index.html");
},array("*","text/html")); // The last parameter on the mapping can override server accepted mimes


$server->addMap("GET","/index",function($server) {
    // For the root access we will present the user interface
    // For this we have a dummy view that just loads up a php file
    return new Rest\View\Generic("ui.php");
}, array("text/html"));

// Here we map POST /users to user creation function
$server->addMap("POST","/users",function($server) {
    // Again we recover set global items
    $db = $server->getParameter("db");
    $salt = $server->getParameter("salt");

    // The full request of the user, including headers and post/put data
    // Is available on the RestRequest object, accesible using 
    // The getRequest() method of the server
    $user = json_decode($server->getRequest()->getBody()); // This is the raw body of the request

    // We them test if it's unique
    $unique = $db->prepare("select count(*) from users where login = ?");
    $unique->execute(array( $user->login ));
    // If not we foward the server to the BadRequest handler
    if($unique->fetchColumn(0) != 0) new \Rest\Controller\BadRequest;

    // Them we really insert it
    $insert = $db->prepare("insert into users (login, password, name) values (?,?,?)");
    $insert->execute(array($user->login,md5($salt.$user->password),$user->name));

    // The response object hold the data that you be sent back to the client
    // You can get it using the getResponse() method of the server instance
    $server->getResponse()->setResponse(json_encode($user)); // Them we send back the supplied user

    // Given a valid insertion we tell the server to respond with a proper 201 Created 
    return new \Rest\Controller\Created;
});


//
// The follow mapping are pretty much the same logic, but with different handlers
// I will comment now only on important diferences
//

// So here is a feature, you can map parts of the url to named parameters
// In this case the sencond part of the url will be on the login parameter
// See more below
$server->addMap("GET","/users/:login",function($server) {
    $db = $server->getParameter("db");
    
    // Here, on the Request, you get the named parameter
    $user =  $server->getRequest()->getParameter("login");

    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute(array($user));
    if($exists->fetchColumn(0) == 0) return new \Rest\Controller\NotFound;

    $login = $server->getAuthenticator()->getUser();
    if($login != $user) return new \Rest\Controller\Forbidden; 

    if(!authenticate($server)) return new \Rest\Controller\NotAuthorized;

    $select = $db->prepare("select login, name from users where login = ?");
    $select->execute(array($user));
    $user = $select->fetchObject();

    // There is a generic useful view for return json data
    // It encodes the data and send proper content-type
    return new \Rest\View\JSon($user); 
});

$server->addMap("PUT","/users/:login",function($server) {
    $db = $server->getParameter("db");
    $salt = $server->getParameter("salt");
    $user =  $server->getRequest()->getParameter("login");
    $login = $server->getAuthenticator()->getUser();
    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute($user);
    if($exists->fetchColumn(0) != 0) return new \Rest\Controller\NotFound;
    if($login != $user) return new \Rest\Controller\Forbidden;
    if(!authenticate($server)) return new \Rest\Controller\NotAuthorized;

    // You can get PUT request just like POST
    $user = json_decode($server->getRequest()->getBody()); 
    $update = $db->prepare("update users set password = ?, name = ? where login = ?");
    $update->execute(array(md5($salt.$user->password),$user->name,$user->login));
    return new \Rest\View\JSon($user);
});

$server->addMap("DELETE","/users/:login",function($server) {
    $db = $server->getParameter("db");
    $user =  $server->getRequest()->getParameter("login");
    $login = $server->getAuthenticator()->getUser();
    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute($user);
    if($exists->fetchColumn(0) != 0) return new \Rest\Controller\NotFound;
    if($login != $user) return new \Rest\Controller\Forbidden;
    if(!authenticate($server)) return new \Rest\Controller\NotAuthorized;

    $delete = $db->prepare("delete from users where login = ?");
    $delete->execute(array($user));
    return new \Rest\View\JSon($user);
});

$server->addMap("GET","/:login/items",function($server) {
});

$server->addMap("POST","/:login/items",function($server) {
});

$server->addMap("PUT","/:login/items/:key",function($server) {
});

$server->addMap("GET","/:login/items/:key",function($server) {
});

$server->addMap("DELETE","/:login/items/:key",function($server) {
});

// This is your last call, it will chain the hole process and display the results :)
$server->execute();

?>
