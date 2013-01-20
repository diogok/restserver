<?php
namespace Rest\Controller;

class NotAcceptable implements \Rest\Controller{
    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("HTTP/1.1 400 BAD REQUEST");
        return $rest;
    }
}

