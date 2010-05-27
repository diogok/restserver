<?php

/**
 * Class RestAuthenticator
 * Responsible for dealing with both Basic and Digest authentication
 */
class RestAuthenticator {

    private $rest ;
    
    private $user ;
    private $pwd ;
    private $authData ;
    private $isDigest =false;
    private $requireAuth =false;
    private $auth;
    private $realm;

    /**
     * RestAuthenticator constructor
     * @param RestServer $rest
     */
    public function __construct(RestServer $rest=null) {
        $this->rest = $rest ;

        if(isset($_SERVER['PHP_AUTH_DIGEST']))
            $this->authData = $_SERVER['PHP_AUTH_DIGEST'] ;                

        if(isset($_SERVER['PHP_AUTH_USER'])) 
            $this->user = $_SERVER["PHP_AUTH_USER"];

        if(isset($_SERVER['PHP_AUTH_PW'])) 
            $this->pwd = $_SERVER["PHP_AUTH_PW"];

        if(isset($_SERVER["HTTP_AUTHORIZATION"])) {
            $base = base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"],6));
            $arr = explode(":",$base);
            $this->user = $arr[0];
            $this->pwd = $arr[1];
        }
        
        if (!empty($this->authData) && ($data = $this->digestParse($this->authData)) && $data['username']) {
            $this->user = $data['username'] ;
            $this->pwd = $data['response'] ;
        }
    }
    
    /**
     * Return internal RestServer
    * Return  RestServer used;
    * @return RestServer
    */
    public function getRest() {
        return $this->rest;
    }
    
    /**
    * Return user sent on BASIC Authentication
    * @return string
    */
    public function getUser() {
        return $this->user;
    }

    /**
    * Return password sent for Authentication
    * @return string
    */
    public function getPassword() {
        return $this->pwd ;
    }
    
    /**
    * Return if is using digest authentication
    * @return bool
    */
    public function isDigest() {
        return $this->isDigest ;
    }

    /**
    * Set if authentication should be Digest(true) 
    * @param bool $bool
    * @param string $realm
    * @return RestAuthenticator
    */
    public function forceDigest($bool=true,$realm=null) {
        if($realm != null) $this->setRealm($realm);
        $this->isDigest = $bool;
        if($bool) {$this->requireAuthentication(true);}
        return $this;
    }

    /**
    * Get the http Realm name
    * @return string $realm
    */
    public function getRealm() {
        return $this->realm;
    }

    /**
    * Set the http Realm name
    * @param string $realm
    * @return RestAuthenticator
    */
    public function setRealm($realm) {
        $this->realm = $realm ;
        return $this;
    }

    /**
    * Sets if authentication is required
    * @param bool $isRequered 
    * @return RestAuthenticator
    */
    public function requireAuthentication($isRequired=true) {
        if($isRequired !== null) $this->requireAuth = $isRequired ;
        return $this ;
    }
    
    /**
    * Checks if authenticated is required
    * @return bool $auth;
    */
    public function isAuthenticationRequired() {
        return $this->requireAuth ;
    }

    /**
    * Checks if is authenticated
    * @return bool $auth;
    */
    public function isAuthenticated() {
        return $this->auth ;
    }

    /**
    * Sets authentication status
    * @param bool $auth Status
    * @return RestServer
    */
    public function setAuthenticated($bool) {
        $this->auth = $bool;
        return $this ;
    }
    
    /**
    * Test if user is authenticated, and set proper headers if not
    * @return bool
    */
    public function tryAuthenticate() {
        if($this->isAuthenticationRequired() === false) return true;
        if($this->isAuthenticated() == false) { 
            $this->getRest()->getResponse()->cleanHeader();
            $this->getRest()->getResponse()->addHeader("HTTP/1.1 401 Unauthorized");
            if($this->isDigest()) {
                $this->getRest()->getResponse()->addHeader('WWW-Authenticate: Digest ' . $this->digestHeader());
            } else {
                $this->getRest()->getResponse()->addHeader('WWW-Authenticate: Basic realm="'.$this->getRealm().'"');
            }
            $this->getRest()->getResponse()->setResponse("Unauthorized");
            return false ;
        }
        return true ;
    }
    
    /**
    * Test authentication against password for given username in Digest 
    * @param string $user
    * @param string $password
    * @return RestAuthenticator
    */
    public function validate($user,$password) {
        if($this->isDigest()) {
            $data = $this->digestParse($this->authData);
            $A1 = md5($this->getUser() . ':' . $this->getRealm() . ':' . $password);
            $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$_SERVER['REQUEST_URI']);
            $response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
            if($this->getUser() === $user && $this->getPassword() === $response) {
                $this->pwd = $password ;
                $this->setAuthenticated(true);
            } else {
                $this->setAuthenticated(false);
            }
        } else {
            if($this->getUser() === $user && $this->getPassword() === $password) {
                $this->setAuthenticated(true);
            } else {
                $this->setAuthenticated(false);
            }
        }
        return $this;
    }
    
    /**
     * Parse the digest auth message
     * @param string $message
     * @return mixed
     */
    private function digestParse($txt) {
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $parts = explode(",",$txt);
        foreach($parts as $part) {
            $div = strpos($part,"=");
            $name = trim(substr($part,0,$div));
            $value = trim(substr($part,$div + 1));
            if($value[0] == "\"") {
                $value = substr($value,1, strlen($value) - 2);
            }
            unset($needed_parts[$name]);
            $data[$name] = $value;
        }

        return $needed_parts ? false : $data;
    }    
    
    /**
     * Digest header
     * @return string
     */
    private function digestHeader() {
        $op = array(
            'realm' => $this->getRealm(),
            'domain' => '/',
            'qop' => 'auth',
            'algorithm' => 'MD5',
            'nonce' => uniqid(),
            'opaque' => md5($this->getRealm()),
            );
        $str = 'realm="'.$op['realm'].'",';
        $str .= 'qop="'.$op['qop'].'",';
        $str .= 'nonce="'.$op['nonce'].'",';
        $str .= 'opaque="'.$op['opaque'].'"';
        return $str;
    }

    
}

?>
