<?php
namespace Rest\Controller;

class NotFound implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 404 NOT FOUND");
        $rest->getResponse()->setResponse("HTTP/1.1 404 NOT FOUND");
        return $rest;
    }
}

