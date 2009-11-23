<?php

include '../../RestServer.class.php';
include 'TestController.class.php';

$rest = new RestServer($_GET['q']) ;

$rest->addMap("GET",".*","TestController");
$rest->addMap("POST",".*","TestController");
$rest->addMap("PUT",".*","TestController");
$rest->addMap("DELETE",".*","TestController");
$rest->addMap("OPTIONS",".*","TestController");

echo $rest->execute();

?>
