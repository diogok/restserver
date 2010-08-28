<?php
include_once '../RestResponse.class.php';
include_once 'PHPUnit/Framework.php';

class RestResponseTest extends PHPUnit_Framework_TestCase {
    
    function testResponse() {
        $r = new RestResponse();
        $this->assertTrue($r->headerSent());
        $r->setResponse("foo");
        $r->appendResponse("bar");
        $this->assertEquals($r->getResponse(),"foobar");
    }

}
?>
