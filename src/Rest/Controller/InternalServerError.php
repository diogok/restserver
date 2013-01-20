<?php
namespace Rest\Controller;

class InternalServerError implements \Rest\Controller{

    function __construct($var=null) {
        $this->dunno = $var;
    }

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 500 INTERNAL SERVER ERROR");
        $rest->getResponse()->setResponse($this->dunno->getMessage());
        return $rest;
    }
}

