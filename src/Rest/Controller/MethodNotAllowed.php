<?php
namespace Rest\Controller;

class MethodNotAllowed implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 405 METHOD NOT ALLOWED");
        $rest->getResponse()->setResponse("HTTP/1.1 405 METHOD NOT ALLOWED");
        return $rest;
    }
}

