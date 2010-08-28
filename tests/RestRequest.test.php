<?php
include_once '../RestRequest.class.php';
include_once 'PHPUnit/Framework.php';

class RestRequestTest extends PHPUnit_Framework_TestCase {

    function setup() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/tests/foo.php";
        $_SERVER["HTTP_ACCEPT"] = "text/html";
    }

    function testMethod() {
        $r = new RestRequest();
        $this->assertFalse($r->isPost());
        $this->assertFalse($r->isPut());
        $this->assertFalse($r->isDelete());
        $this->assertTrue($r->isGet());
        $this->assertEquals($r->getMethod(),"GET");
    }

    function testVars() {
        $_GET["a"] = "aaa";
        $_POST["foo"] = "bar";
        $_FILES["bar"] = "foo";
        $r = new RestRequest();
        $this->assertEquals($r->getGet("a"),"aaa");
        $this->assertEquals($r->getPost("foo"),"bar");
        $this->assertEquals($r->getFiles("bar"),"foo");
    }

    function testUri() {
        $r = new RestRequest();
        $t = count(explode("/",$_SERVER["REQUEST_URI"])) - 2;
        $this->assertEquals($r->getURI($t),"tests");
        $this->assertEquals($r->getURIPart($t),"tests");
        $r->setURI("/test/me/now.html");
        $this->assertEquals($r->getURI(2),"me");
        $this->assertEquals($r->getURIPart(3),"now.html");
    }

    function testTypes() {
        $r = new RestRequest();
        $this->assertTrue($r->acceptMime("text/html"));
        $this->assertFalse($r->acceptMime("application/json"));
        $this->assertEquals($r->getExtension(),"php");
    }

}

?>
