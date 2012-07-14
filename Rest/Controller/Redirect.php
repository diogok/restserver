<?php
namespace Rest\Controller;

class Redirect implements \Rest\Controller{

    function __construct($var=null) {
        $this->dunno = $var;
    }

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addheader("Location: ".$this->dunno);
        $rest->getResponse()->cleanResponse();
        return $rest;
    }
}

