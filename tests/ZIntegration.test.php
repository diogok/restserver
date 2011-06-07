<?php
include_once '../RestServer.class.php';
include_once '../RestClient.class.php';
include_once 'PHPUnit/Framework.php';

class ZIntegrationTest extends PHPUnit_Framework_TestCase {

    function setup() {
        //$r = new RestServer();
        //$this->url = $r->getBaseUrl()."/server.php?q=";
        $this->url = "http://localhost/diogo/restserver/tests/server.php?q=";
    }

    function testSimpleGetLambda() {
        $c = RestClient::get($this->url."/Lambda");
        $this->assertEquals($c->getResponse(),"Hello Closure!");
    }
    function testSimpleGet() {
        $c = RestClient::get($this->url."/Foo");
        $this->assertEquals($c->getResponse(),"Hello World!");
    }

    function testSimpleGet2() {
        $c = RestClient::get($this->url."/Foo/bar");
        $part = substr($c->getResponse(),0,10);
        $this->assertEquals($part,"Method=GET");
    }

    function test404() {
        $c = RestClient::get($this->url."/Foo/rar");
        $this->assertEquals($c->getResponseCode(),404);
    }

    function testSimplePosWith201() {
        $c = RestClient::post($this->url."/Foo");
        $this->assertEquals($c->getResponseCode(),201);
    }

    function testGetWithExtras() {
        $c = RestClient::get($this->url."/Foo/hello/diogo");
        $this->assertEquals($c->getResponse(),"Hello , Diogo.");
    }

    function testPostWithParams() {
        $c = RestClient::post($this->url."/Foo/hello",array("name"=>"diogo"));
        $this->assertEquals($c->getResponse(),"Hello , Diogo.");
    }

    function testBasicAuth()  {
        $c = RestClient::get($this->url."/Foo/restricted/basic",null,"joe","123");
        $this->assertEquals($c->getResponse(),"joe");
        $c = RestClient::get($this->url."/Foo/restricted/basic",null,"joe","321");
        $this->assertEquals($c->getResponseCode(),401);
    }

    function testDigestAuth()  {
        $c = RestClient::get($this->url."/Foo/restricted/digest");
        $this->assertEquals($c->getResponseCode(),401);
    }

    function testUnpreciseBench() {
        $c = RestClient::get($this->url."/Foo/bench");
        echo $c->getResponse();
    }

}

?>
