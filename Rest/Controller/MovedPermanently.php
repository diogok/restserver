<?php
namespace Rest\Controller;

class MovedPermanently implements \Rest\Controller{

    function __construct($var=null) {
        $this->dunno = $var;
    }

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->cleanResponse();
        $rest->getResponse()->addHeader("HTTP/1.1 301 MOVED PERMANENTLY");
        $rest->getResponse()->addheader("Location: ".$this->dunno);
        $rest->getResponse()->cleanResponse();
        return $rest;
    }
}

