<?php

include_once '../Rest/Server.php';
include_once '../Rest/Client.php';
include_once '../tests/simpletest/autorun.php';

class Example extends UnitTestCase {

    function setup() {
        $this->url = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"])."/../example";
    }

    function testUserInsertion() {
        $c = Rest\Client::post($this->url."/users",json_encode(array("login"=>"diogok","password"=>"123456","name"=>"Diogo Souza")),null,null,"application/json");
        $this->assertEqual($c->getResponseCode(),201);
    }

    function testNonAuthenticatedUserCall() {
        $c = Rest\Client::get($this->url."/users/diogok");
        $this->assertEqual($c->getResponseCode(),403);
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123");
        $this->assertEqual($c->getResponseCode(),401);
    }

    function testValidGetUser() {
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),200);
        $u = json_decode($c->getResponse());
        $this->assertEqual($u->name,"Diogo Souza");
    }

    function testNonExistantUser() {
        $c = Rest\Client::get($this->url."/users/diogokid",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),404);
    }

    function testCanUpdate() {
        $c = Rest\Client::put($this->url."/users/diogok",json_encode(array("login"=>"diogok","password"=>"123456","name"=>"Diogo Silva")),"diogok","123456","application/json");
        $this->assertEqual($c->getResponseCode(),200);
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),200);
        $u = json_decode($c->getResponse());
        $this->assertEqual($u->name,"Diogo Silva");
    }

    function testCanDeleteUser() {
        $c = Rest\Client::delete($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),200);
        $c = Rest\Client::get($this->url."/users/diogok",null,"diogok","123456");
        $this->assertEqual($c->getResponseCode(),404);
    }


}

?>
