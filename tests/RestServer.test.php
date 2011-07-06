<?php

include_once '../RestServer.class.php';

class RestServerTest extends PHPUnit_Framework_TestCase {
    
    function setup() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/tests/foo.php";
        $_SERVER["HTTP_ACCEPT"] = "text/html";
    }

    function testParameter() {
        $r = new RestServer ;
        $r->setParameter("foo","bar");
        $this->assertEquals($r->getParameter("foo"),"bar");
    }

    function testQueries(){
        $r = new RestServer ;
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEquals($r->getQuery($t),"tests");
        $this->assertEquals($r->getRequest()->getURI($t),"tests");
        $r->setQuery("/teste/me");
        $this->assertEquals($r->getQuery(2),"me");
        $this->assertEquals($r->getRequest()->getURI(2),"me");
    }

    function testMapping() {
        $r = new RestServer;
        $r->addMap("GET","/user","Foo");
        $r->addMap("GET","/user/diogo","Bar");
        $r->addMap("GET","/user/[0-9]+","Hal");
        $r->addMap("GET","/user/[a-z]+","Hal2");
        $r->addMap("GET","/user/[a-z]+/profile","Hal2::profile");
        $r->addMap("GET","/user2/:name","Named");
        $r->addMap("GET","/user2/:name/:id","Named2");
        $r->setQuery("/user");
        $this->assertEquals($r->getMap("GET","/user"),"Foo");
        $this->assertEquals($r->getMap("GET","/user/diogo"),"Bar");
        $this->assertEquals($r->getMap("GET","/user/123"),"Hal");
        $this->assertEquals($r->getMap("GET","/user/abc"),"Hal2");
        $this->assertEquals($r->getMap("GET","/user/abc/profile"),"Hal2::profile");
        $this->assertEquals($r->getMap("GET","/user/123abc"),null);
        $this->assertEquals($r->getMap("GET","/user2/abc"),"Named");
        $this->assertEquals($r->getMap("GET","/user2/abc/123"),"Named2");
    }

}
?>
