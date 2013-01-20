<?php

include_once '../src/Rest/Authenticator.php';
include_once 'simpletest/autorun.php';

class RestAuthenticatorTest extends UnitTestCase {
    function testFlags() {
        $r = new Rest\Authenticator();
        $this->assertFalse($r->isDigest());
        $r->setRealm("foo");
        $this->assertEqual($r->getRealm(),"foo");
        $r->forceDigest(true);
        $this->assertTrue($r->isAuthenticationRequired());
        $this->assertTrue($r->isDigest());
        $this->assertEqual($r->getRealm(),"foo");
        $r->forceDigest(true,"hiho");
        $this->assertEqual($r->getRealm(),"hiho");
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
