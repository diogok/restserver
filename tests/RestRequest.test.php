<?php
include_once 'simpletest/autorun.php';
include_once '../RestRequest.class.php';

class RestRequest_tests extends UnitTestCase {
    
    function testMethod() {
        $r = new RestRequest();
        $this->assertFalse($r->isPost());
        $this->assertFalse($r->isPut());
        $this->assertFalse($r->isDelete());
        $this->assertTrue($r->isGet());
        $this->assertEqual($r->getMethod(),"GET");
    }

    function testVars() {
        $_GET["a"] = "aaa";
        $_POST["foo"] = "bar";
        $_FILES["bar"] = "foo";
        $r = new RestRequest();
        $this->assertEqual($r->getGet("a"),"aaa");
        $this->assertEqual($r->getPost("foo"),"bar");
        $this->assertEqual($r->getFiles("bar"),"foo");
    }

    function testUri() {
        $r = new RestRequest();
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEqual($r->getURI($t),"tests");
        $this->assertEqual($r->getURIPart($t),"tests");
        $r->setURI("/test/me/now.html");
        $this->assertEqual($r->getURI(2),"me");
        $this->assertEqual($r->getURIPart(3),"now.html");
    }

    function testTypes() {
        $r = new RestRequest();
        $this->assertTrue($r->acceptMime("text/html"));
        $this->assertFalse($r->acceptMime("application/json"));
        $this->assertEqual($r->getExtension(),"php");
    }

}

?>
