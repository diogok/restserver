<?php
namespace Rest\Controller;

class Forbidden implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 403 FORBIDDEN");
        return $rest;
    }
}

