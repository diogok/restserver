<?php
include_once '../Rest/Response.php';
include_once 'simpletest/autorun.php';

class RestResponseTest extends UnitTestCase {
    
    function testResponse() {
        $r = new Rest\Response();
        $r->setResponse("foo");
        $r->appendResponse("bar");
        $this->assertEqual($r->getResponse(),"foobar");
    }

}
?>
