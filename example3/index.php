<?php

include '../RestServer.class.php';
include 'UserController.class.php';

$rest = new RestServer($_GET['q']) ;

$rest->addMap("GET","/?users","UserController::list");
$rest->addMap("POST","/?users","UserController::insert");
$rest->addMap("GET","/?users/[0-9]*","UserController::view");

// Show the response
echo $rest->execute();

?>
