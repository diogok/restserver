<?php
include_once '../RestServer.class.php';
include_once '../RestClient.class.php';
include_once 'simpletest/autorun.php';

class ZIntegration_tests extends UnitTestCase {

    function setup() {
        $r = new RestServer();
        $this->url = $r->getBaseUrl()."/server.php?q=";
    }

    function testSimpleGet() {
        $c = RestClient::get($this->url."/Foo");
        $this->assertEqual($c->getResponse(),"Hello World!");
    }

    function testSimpleGet2() {
        $c = RestClient::get($this->url."/Foo/bar");
        $part = substr($c->getResponse(),0,10);
        $this->assertEqual($part,"Method=GET");
    }

    function test404() {
        $c = RestClient::get($this->url."/Foo/rar");
        $this->assertEqual($c->getResponseCode(),404);
    }

    function testSimplePosWith201() {
        $c = RestClient::post($this->url."/Foo");
        $this->assertEqual($c->getResponseCode(),201);
    }

    function testGetWithExtras() {
        $c = RestClient::get($this->url."/Foo/hello/diogo");
        $this->assertEqual($c->getResponse(),"Hello , Diogo.");
    }

    function testPostWithParams() {
        $c = RestClient::post($this->url."/Foo/hello",array("name"=>"diogo"));
        $this->assertEqual($c->getResponse(),"Hello , Diogo.");
    }

    function testBasicAuth()  {
        $c = RestClient::get($this->url."/Foo/restricted/basic",null,"joe","123");
        $this->assertEqual($c->getResponse(),"joe");
        $c = RestClient::get($this->url."/Foo/restricted/basic",null,"joe","321");
        $this->assertEqual($c->getResponseCode(),401);
    }

    function testDigestAuth()  {
        $c = RestClient::get($this->url."/Foo/restricted/digest");
        $this->assertEqual($c->getResponseCode(),401);
    }

    function testUnpreciseBench() {
        $c = RestClient::get($this->url."/Foo/bench");
        echo $c->getResponse();
    }

}

?>
