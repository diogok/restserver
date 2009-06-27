<?

include '../../RestServer.class.php';
include 'TestController.class.php';

$rest = new RestServer($_GET['q']) ;

$rest->addMap("GET",".*","TestController");
$rest->addMap("POST",".*","TestController");

echo $rest->execute();

?>
