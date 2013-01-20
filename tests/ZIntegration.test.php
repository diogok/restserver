<?php
include_once '../src/Rest/Server.php';
include_once '../src/Rest/Client.php';
include_once 'simpletest/autorun.php';

class ZIntegrationTest extends UnitTestCase {

    function setup() {
        $r = new Rest\Server();
        $this->url = $r->getBaseUrl()."/server.php?q=";
    }

    function testSimpleGetLambda() {
        $c = Rest\Client::get($this->url."/Lambda");
        $this->assertEqual($c->getResponse(),"Hello Closure!");
    }

    function testSimpleGet() {
        $c = Rest\Client::get($this->url."/Foo");
        $this->assertEqual($c->getResponse(),"Hello World!");
    }

    function testSimpleGet2() {
        $c = Rest\Client::get($this->url."/Foo/bar");
        $part = substr($c->getResponse(),0,10);
        $this->assertEqual($part,"Method=GET");
    }

    function test404() {
        $c = Rest\Client::get($this->url."/Foo/rar");
        $this->assertEqual($c->getResponseCode(),404);
    }

    function testSimplePosWith201() {
        $c = Rest\Client::post($this->url."/Foo");
        $this->assertEqual($c->getResponseCode(),201);
    }

    function testGetWithExtras() {
        $c = Rest\Client::get($this->url."/Foo/hello/diogo");
        $this->assertEqual($c->getResponse(),"Hello , Diogo.");
    }

    function testPostWithParams() {
        $c = Rest\Client::post($this->url."/Foo/hello",array("name"=>"diogo"));
        $this->assertEqual($c->getResponse(),"Hello , Diogo.");
    }

    function testBasicAuth()  {
        $c = Rest\Client::get($this->url."/Foo/restricted/basic",null,"joe","123");
        $this->assertEqual($c->getResponse(),"joe");
        $c = Rest\Client::get($this->url."/Foo/restricted/basic",null,"joe","321");
        $this->assertEqual($c->getResponseCode(),401);
    }

    function testDigestAuth()  {
        $c = Rest\Client::get($this->url."/Foo/restricted/digest");
        $this->assertEqual($c->getResponseCode(),401);
    }

    function testNamed() {
        $c = Rest\Client::get($this->url."/hello/Gi");
        $this->assertEqual($c->getResponse(),"Hello, Gi!");
    }

    function testExtension() {
        $c = Rest\Client::get($this->url."/mime");
        $this->assertEqual($c->getResponse(),"Hello, !");
        $c = Rest\Client::get($this->url."/mime.html");
        $this->assertEqual($c->getResponse(),"Hello, html!");
        $c = Rest\Client::get($this->url."/nomime");
        $this->assertEqual($c->getResponseCode(),400);
        $c = Rest\Client::get($this->url."/nomime.html");
        $this->assertEqual($c->getResponse(),"Hello, html!");
    }

    function testUnpreciseBench() {
        $c = Rest\Client::get($this->url."/Foo/bench");
        echo $c->getResponse();
    }


}

?>
