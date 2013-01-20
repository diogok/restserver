<?php
namespace Rest\Controller;

class Generic implements \Rest\Controller{

    function __construct($var=null) {
        $this->dunno = $var;
    }

    function execute(\Rest\Server $rest) {
        return call_user_func($this->dunno,$rest);
    }

    function NotFound($rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 404 NOT FOUND");
        $rest->getResponse()->setResponse("HTTP/1.1 404 NOT FOUND");
        return $rest;
    }

    function Options($rest) {
        $rest->getResponse()->addHeader("Allow: ".$rest->getParameter("opts"));
        $rest->getResponse()->setResponse("Allow: ".$rest->getParameter("opts"));
        return $rest;
    }

    function Head($rest) {
        $rest->getRequest()->setMethod("GET");
        $rest->execute(false);
        return $rest;
    }

    function NotModified($rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 304 NOT MODIFIED");
        $rest->getResponse()->setResponse("HTTP/1.1 304 NOT MODIFIED");
        return $rest;
    }

    function MethodNotAllowed($rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 405 METHOD NOT ALLOWED");
        $rest->getResponse()->setResponse("HTTP/1.1 405 METHOD NOT ALLOWED");
        return $rest;
    }
}

