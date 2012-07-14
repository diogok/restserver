<?php

include_once '../Rest/Server.php';
include_once '../Rest/Client.php';
include_once '../tests/simpletest/autorun.php';

class Example extends UnitTestCase {

    function setup() {
        $this->url = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"])."/../example/index.php?q=";
    }

    function testCRUDUsers() {
        $c = Rest\Client::post($this->url."/users",json_encode(array("login"=>"diogok","password"=>"123456","name"=>"Diogo")),null,null,"application/json");
        $this->assertEqual($c->getResponseCode(),201);
        $c = Rest\Client::get($this->url."/users/diogok");
        $this->assertEqual($c->getResponseCode(),401);
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),200);
        $u = json_decode($c->getResponse());
        $this->assertEqual($u->name,"Diogo");
        $c = Rest\Client::get($this->url."/users/diogokid",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),404);
        $c = Rest\Client::delete($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),200);
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),401);
    }

}

?>
