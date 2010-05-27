<?php

include_once 'simpletest/autorun.php';
include_once '../RestServer.class.php';

class RestServer_tests extends UnitTestCase {
    
    function testParameter() {
        $r = new RestServer ;
        $r->setParameter("foo","bar");
        $this->assertEqual($r->getParameter("foo"),"bar");
    }

    function testQueries(){
        $r = new RestServer ;
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEqual($r->getQuery($t),"tests");
        $this->assertEqual($r->getRequest()->getURI($t),"tests");
        $r->setQuery("/teste/me");
        $this->assertEqual($r->getQuery(2),"me");
        $this->assertEqual($r->getRequest()->getURI(2),"me");
    }

    function testMapping() {
        $r = new RestServer;
        $r->addMap("GET","/user","Foo");
        $r->addMap("GET","/user/diogo","Bar");
        $r->addMap("GET","/user/[0-9]+","Hal");
        $r->addMap("GET","/user/[a-z]+","Hal2");
        $r->addMap("GET","/user/[a-z]+/profile","Hal2::profile");
        $r->setQuery("/user");
        $this->assertEqual($r->getMap("GET","/user"),"Foo");
        $this->assertEqual($r->getMap("GET","/user/diogo"),"Bar");
        $this->assertEqual($r->getMap("GET","/user/123"),"Hal");
        $this->assertEqual($r->getMap("GET","/user/abc"),"Hal2");
        $this->assertEqual($r->getMap("GET","/user/abc/profile"),"Hal2::profile");
        $this->assertEqual($r->getMap("GET","/user/123abc"),null);
    }

}
?>
