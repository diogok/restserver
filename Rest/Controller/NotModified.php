<?php
namespace Rest\Controller;

class NotModified implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 304 NOT MODIFIED");
        $rest->getResponse()->setResponse();
        return $rest;
    }
}

