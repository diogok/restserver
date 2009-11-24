<?php
// Now, let the autoload do it's job
include 'autoLoad.php';
include '../RestServer.class.php';
include '../GenericView.class.php';

// As we use .htaccess, and if we are first diretory(/), RestServer can get the URL
$rest = new RestServer($_GET["q"]) ;

/**
  * About the .htaccess, as we put the uri in the $_GET["q"] if we are the main directory it will work
  * For example, http://website.com/my/url , with this .htacces RestServer will use "/my/url" as parameter
  * If not, we can set the url with the following code
  */
//$rest->getRequest()->setURI($_GET["q"]);

// Maps the urls and methods
$rest->addMap("GET","/?","HomeController");
// We can use fluent interface
$rest->addMap("GET","/user","UserController")->addMap("GET","/user/[0-9]+","UserController::profile"); 
$rest->addMap("POST","/user","UserController::insert")->addMap("POST","/user/[0-9]+","UserController::update");
// As some may disagre on a POST for update, you can allow PUT ;)
$rest->addMap("PUT","/user/[0-9]+","UserController::update");

// Show the response
echo $rest->execute();

?>
