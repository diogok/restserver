<?php

/** Class RestRequest
  * Holds the Request in a RestServer
  */
class RestRequest {

    private $rest ;

    private $requestURI ;
    private $URIParts ; 
        
    private $user ;
    private $pwd ;
    private $authData ;
	
	private $requestMethod ;
	private $get ;
	private $post ;
    private $files ;
	
    /**
      * Constructor of RestRequest
      * @param RestServer $rest = null, Parent RestServer
      */
    public function __construct(RestServer $rest=null) {

        // Sets most of the parameters
        $this->rest = $rest ;
            
        if(isset($_SERVER["REQUEST_METHOD"]))
            $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        if(isset($_SERVER["REQUEST_URI"]))
            $this->requestURI = $_SERVER["REQUEST_URI"];
        $this->URIParts = explode("/",$this->requestURI);
                
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

	    $authData = $this->authData ;
	    if (!empty($authData) && ($data = $this->digestParse($authData)) && $data['username']) {
            $this->user = $data['username'] ;
            $this->password = $data['response'] ;
        }

		$this->get = $_GET ;
		$this->post = $_POST ;
        $this->files = $_FILES ;
		
    }
 
    /**
      * Test authentication against password for given username in Digest authencitation
      * @param string $user
      * @param string $password
      * @return RestRequest 
      */
    public function validAuth($user,$password) {
        $bkp = $password;
        if($this->rest->isDigest()) {
            $authData = $this->authData ;
            if (!empty($authData) && ($data = $this->digestParse($authData)) && $data['username']) {
                $A1 = md5($this->getUser() . ':' . $this->getRest()->getRealm() . ':' . $password);
                $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$_SERVER['REQUEST_URI']);
                $password = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
            }
        }
        if($this->getUser() === $user && $this->getPassword() === $password) {
            $this->getRest()->setAuth(true);
            $this->password = $password ;
            $this->password = $pwd ;
        }
        return $this->getRest();
    }

    /**
      * Return  RestServer used;
      * @return RestServer
      */
    public function getRest() {
        return $this->rest;
    }
    private function digestParse($txt) {
        // protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        // fix elements with missing "
        $txt = preg_replace('@=([^\'"])([^\s,]+)@', '="\1\2"', $txt);
        preg_match_all('@(\w+)=(?:([\'"])([^\2]+)\2|([^\s,]+))@U', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = trim($m[3] ? $m[3] : $m[4]);
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

    /**
      * Returns if Request is GET
      * @return boolean
      */
    public function isGet() {
        if($this->requestMethod == "GET") {
            return true ;
        }
        return false;
    }

    /** 
      * Returns if Request is POST
      * @return boolean
      */
    public function isPost() {
       if($this->requestMethod == "POST") {
           return true ;
       }
       return false;
    }

    /**
      * Return if Request is PUT
      * @return boolean
      */
    public function isPut() {
       if($this->requestMethod == "PUT") {
           return true ;
       }
       return false;
    }

    /**
      * Return true if Request is DELETE
      * @return boolean
      */
    public function isDelete() {
       if($this->requestMethod == "DELETE") {
           return true ;
       }
       return false;
    }
	

    /** 
      * Get parameters sent with GET (url parameters)
      * @return array
      */
    public function getGet($k=null) {
        if($k==null) return $this->get ;
        else return $this->get[$k] ;
    }

    /**
      * Return parameters sent on a POST
      * @return array
      */
    public function getPost($k=null) {
        if($k==null) return $this->post ;
        else return $this->post[$k] ;
    }

    public function getFiles($k=null) {
        if($k==null) return $this->files ;
        else return $this->files[$k];
    }

    /**
      * Return content sent with PUT
      * @param $key=null
      * @return mixed 
      */
    public function getPut($k=null) {
       $_PUT  = array();
       if($_SERVER['REQUEST_METHOD'] == 'PUT') {
           $putdata = file_get_contents('php://input');
           $exploded = explode('&', $putdata); 
           foreach($exploded as $pair) {
               $item = explode('=', $pair);
               if(count($item) == 2) {
                   $_PUT[urldecode($item[0])] = urldecode($item[1]);
               }
           }
      }
      if($k==null)return $_PUT ;
      else return $_PUT[$k];
    }


    /**
      * Return request BODY
      * @return string 
      */
    public function getBody() {
      $data = file_get_contents('php://input');
      return $data;
    }

    /**
      * Get authentication data on DIGEST
      * @return mixed
      */
   public function getAuthData() {
      return $this->authData;
   }
   
   /**
     * Return user sent on BASIC Authentication
     * @return string
     */
   public function getUser() {
       return $this->user;
   }
        
   /**
     * Return password sent on Basic Authentication
     * @return string
     */
   public function getPassword() {
       return $this->pwd ;
   }
    
   /**
     * Return Request Method(PUT, DELETE, OPTION, GET...)
     * return string
     */
   public function getMethod() {
      return $this->requestMethod ;
   }
        
   /**
     * Set request method
     * @param string $method
     * @return  RestRequest
     */
   public function setMethod($m) {
       $this->requestMethod = $m ;
       return $this;
   }
	
   /**
     * Return the URI requested
     * @return string
     */
   public function getRequestURI() {
       return $this->requestURI ;
   }

   /**
     * Return part of the URL
     * return string
     */
   public function getURIpart($i) {
       if(isset($this->URIParts[$i]))
            return $this->URIParts[$i];
        else
            return null;
   }

   /**
     * Return the URI or part of it
     * @param $part=null, count of the part
     * @return string
     */
   public function getURI($i=null) {
        if($i !== null) return $this->getURIpart($i);
        return $this->getRequestURI() ;
   }

   /**
     * Sets the URI to deal
     * @param string $uri
     * @return RestRequest $url
     */
   public function setURI($url) {
		$this->requestURI = $url;
        $this->URIParts = explode("/",$this->requestURI);
        return $this ;
   }

   /**
     * Return the extension of the URI (if any)
     * @return string
     */
   public function getExtension() {
       $reg = array();
       preg_match('@\.([a-zA-Z0-9]{1,5})$@',$this->rest->getQuery(),$reg);
       if(isset($reg[1]))
           return $reg[1];
       else
           return false;
   }
	
   /**
     * Return true if given mime is accepted
     * @param string $mime to check
     * @return boolean
     */
   public function acceptMime($mime) {
        if(strpos($_SERVER["HTTP_ACCEPT"],$mime) > 0) {
            return true ;
        } else {
            return false ;
        }
   }

}
?>
