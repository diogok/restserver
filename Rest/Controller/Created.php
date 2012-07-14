<?php
namespace Rest\Controller;

class Created implements \Rest\Controller{

    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 201 CREATED");
        return $rest;
    }
}

