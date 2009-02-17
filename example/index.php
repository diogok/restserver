<?

// These are the package classes, should be dealt in autoload
include '../RestServer.class.php';
include '../RestAction.class.php';
include '../RestView.class.php';
include '../RestController.class.php';
include '../GenericView.class.php';
include '../RestRequest.class.php';
include '../RestResponse.class.php';

// These are the Example classes, autoLoad it
include 'HomeController.class.php';
include 'UserController.class.php';
include 'UserProfile.class.php';

/** Sample aplication showing how to route with RestServer */

$_GET["q"] = ($_GET["q"] != null)?$_GET["q"]:"";

$rest = new RestServer($_GET["q"]);// Using a parameter as we won't have url rewrite here

/**
* Follows the method addMap(METHOD,URL,CONTROLLER)
* METHOD area the http methods like GET, POST, DELETE, PUT, OPTIONS...
* URL is a regular expression pearl compatible for the url pattern
* CONTROLLER is the RestController class to deal with the url, 
* may specify the method to call, or execute will be called.
*/
$rest->addMap("GET","/?","HomeController");
$rest->addMap("GET","/user","UserController");
$rest->addMap("POST","/user","UserController::insert"); // A specific method
$rest->addMap("GET","/user/[a-zA-Z0-9]+","UserProfile");

echo $rest->execute();

?>
