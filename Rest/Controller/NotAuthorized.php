<?php
namespace Rest\Controller;

class NotAuthorized implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 401 NOT AUTHORIZED");
        $rest->getResponse()->cleanResponse();
        return $rest;
    }
}

