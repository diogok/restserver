<?php
include_once '../Rest/Server.php';
include_once 'simpletest/autorun.php';

class RestRequestTest extends UnitTestCase {

    function setup() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/tests/foo.php";
        $_SERVER["HTTP_ACCEPT"] = "text/html";
    }

    function testMethod() {
        $r = new Rest\Request();
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
        $r = new Rest\Request();
        $this->assertEqual($r->getGet("a"),"aaa");
        $this->assertEqual($r->getPost("foo"),"bar");
        $this->assertEqual($r->getFiles("bar"),"foo");
    }

    function testUri() {
        $r = new Rest\Request();
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEqual($r->getURI($t),"tests");
        $r->setURI("/test/me/now.html");
        $this->assertEqual($r->getURI(2),"me");
        $this->assertEqual($r->getURI(3),"now.html");
    }

    function testNamed() {
        $rs = new Rest\Server ;
        $rs->setMatch(array("","user",":me"));
        $r = $rs->getRequest();
        $r->setURI("/user/diogo");
        $this->assertEqual($r->getURI("me"),"diogo");
        $this->assertEqual($r->getURI("user"),null);
    }

    function testTypes() {
        $r = new Rest\Request();
        $this->assertTrue($r->acceptMime("text/html"));
        $this->assertFalse($r->acceptMime("application/json"));
        $this->assertEqual($r->getExtension(),"php");
    }

}

?>
