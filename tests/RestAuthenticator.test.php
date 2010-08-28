<?php
include_once '../RestAuthenticator.class.php';
include_once 'PHPUnit/Framework.php';


class RestAuthenticatorTest extends PHPUnit_Framework_TestCase {
    function testFlags() {
        $r = new RestAuthenticator();
        $this->assertFalse($r->isDigest());
        $r->setRealm("foo");
        $this->assertEquals($r->getRealm(),"foo");
        $r->forceDigest(true);
        $this->assertTrue($r->isAuthenticationRequired());
        $this->assertTrue($r->isDigest());
        $this->assertEquals($r->getRealm(),"foo");
        $r->forceDigest(true,"hiho");
        $this->assertEquals($r->getRealm(),"hiho");
        $r->forceDigest(false);
        $this->assertFalse($r->isDigest());
        $this->assertTrue($r->isAuthenticationRequired());
        $r->requireAuthentication(false);
        $this->assertFalse($r->isAuthenticationRequired());
        $r->requireAuthentication(true);
        $this->assertTrue($r->isAuthenticationRequired());
        $this->assertFalse($r->isAuthenticated());
        $r->setAuthenticated(true);
        $this->assertTrue($r->isAuthenticated());
        $r->validate(null,"bar");
        $this->assertFalse($r->isAuthenticated());
        $r->validate("foo",null);
        $this->assertFalse($r->isAuthenticated());
        $r->validate("foo","bar");
        $this->assertFalse($r->isAuthenticated());
        
    }
} 
?>
