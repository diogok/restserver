<?php

include_once '../Rest/Server.php';
include_once 'simpletest/autorun.php';

class RestServerTest extends UnitTestCase {
    
    function setup() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/tests/foo.php";
        $_SERVER["HTTP_ACCEPT"] = "text/html";
    }

    function testParameter() {
        $r = new Rest\Server ;
        $r->setParameter("foo","bar");
        $this->assertEqual($r->getParameter("foo"),"bar");
    }

    function testQueries(){
        $r = new Rest\Server ;
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEqual($r->getQuery($t),"tests");
        $this->assertEqual($r->getRequest()->getURI($t),"tests");
        $r->setQuery("/teste/me");
        $this->assertEqual($r->getQuery(2),"me");
        $this->assertEqual($r->getRequest()->getURI(2),"me");
    }

    function testMapping() {
        $r = new Rest\Server;
        $r->addMap("GET","/user","Foo");
        $r->addMap("GET","/user/diogo","Bar");
        $r->addMap("GET","/user/[0-9]+","Hal");
        $r->addMap("GET","/user/[a-z]+","Hal2");
        $r->addMap("GET","/user/[a-z]+/profile","Hal2::profile");
        $r->addMap("GET","/user2/:name","Named");
        $r->addMap("GET","/user2/:name/:id","Named2");
        $r->setQuery("/user");
        $this->assertEqual($r->getMap("GET","/user"),"Foo");
        $this->assertEqual($r->getMap("GET","/user/diogo"),"Bar");
        $this->assertEqual($r->getMap("GET","/user/123"),"Hal");
        $this->assertEqual($r->getMap("GET","/user/abc"),"Hal2");
        $this->assertEqual($r->getMap("GET","/user/abc/profile"),"Hal2::profile");
        $this->assertEqual($r->getMap("GET","/user/123abc"),"\\Rest\\Controller\\NotFound");
        $this->assertEqual($r->getMap("GET","/user2/abc"),"Named");
        $this->assertEqual($r->getMap("GET","/user2/abc/123"),"Named2");
    }

    function testMimeTypes() {
        $r = new Rest\Server;
        $r->setAccept(array("*","text/html","application/json"));
        $r->addMap("GET","/user","Foo");
        $r->addMap("GET","/user/diogo","Bar",array("application/json"));
        $this->assertEqual($r->getMap("GET","/user",false),"Foo");
        $this->assertEqual($r->getMap("GET","/user.html",'html'),"Foo");
        $this->assertEqual($r->getMap("GET","/user.json",'json'),"Foo");
        $this->assertEqual($r->getMap("GET","/user.xml",'xml'),"\\Rest\\Controller\\NotAcceptable");
        $this->assertEqual($r->getMap("GET","/user/diogo.json",'json'),"Bar");
        $this->assertEqual($r->getMap("GET","/user/diogo.html",'html'),"\\Rest\\Controller\\NotAcceptable");
    }

}
?>
