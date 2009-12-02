<?php
include_once 'simpletest/autorun.php';
include_once '../RestResponse.class.php';

class RestResponse_Tests extends UnitTestCase {
    
    function testResponse() {
        $r = new RestResponse();
        $this->assertTrue($r->headerSent());
        $r->setResponse("foo");
        $r->appendResponse("bar");
        $this->assertEqual($r->getResponse(),"foobar");
    }

}
?>
