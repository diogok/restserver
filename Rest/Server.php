<?php

namespace Rest;

include_once 'Action.php';
include_once 'Controller.php';
include_once 'View.php';
include_once 'Request.php';
include_once 'Response.php';
include_once 'Authenticator.php';
include_once 'Client.php';

include_once 'Controller/Generic.php';
include_once 'Controller/Created.php';
include_once 'Controller/MovedPermanently.php';
include_once 'Controller/NotModified.php';
include_once 'Controller/BadRequest.php';
include_once 'Controller/Forbidden.php';
include_once 'Controller/NotFound.php';
include_once 'Controller/NotAuthorized.php';
include_once 'Controller/MethodNotAllowed.php';
include_once 'Controller/NotAcceptable.php';
include_once 'Controller/InternalServerError.php';
include_once 'Controller/Redirect.php';

include_once 'View/Generic.php';
include_once 'View/JSon.php';

/**
* Class Rest\Server 
* Is the front controller for mapping URL to controllers and dealing with Request/Response and Headers
* Made with Restful webservices in mind.
* By Diogo Souza da Silva <manifesto@manifesto.blog.br>
*/

class Server {

    private $response ;
    private $request ;
    private $authenticator ;

    private $baseUrl ; 
    private $query ;

    private $map ;
    private $matched ;
    private $params ;
    private $stack ;

    private $acceptMimes ;

    /**
     * Contructor of Rest\Server
     * @param string $query Optional query to be treat as the URL
     * @return Rest\Server $rest;
     */
    public function __construct($query=null) {
        $this->request = new Request($this); // Request handler
        $this->response = new Response($this); // Response holder
        $this->authenticator = new Authenticator($this); // Authenticator holder

        if(isset($_SERVER["HTTP_HOST"])) {
            $this->baseUrl = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
        }

        // If will use custom URI or HTTP requested URI
        if($query===null) $this->query = $this->getRequest()->getRequestURI() ;
        else $this->query = $query ;

        $this->getRequest()->setURI($this->query);

        $this->matched = false;

        $this->params = array();
    }

   /**
    * Sets a parameter in a global scope that can be recovered at any request.
    * @param mixed $key The identifier of the parameter
    * @param mixed $value The content of the parameter
    * @return Rest\Server $this
    */
    public function setParameter($key,$value) {
        $this->params[$key] = $value ;
        return $this ;
    }

   /**
    * Return all parameters
    * @return mixed
    */
    public function getParameters() {
        return $this->params;
    }

   /**
    * Return the specified parameter
    * @param mixed $key The parameter identifier
    * @return mixed
    */
    public function getParameter($key) {
        return $this->params[$key];
    }

   /** 
    * Maps a Method and URL for a Class
    * @param string $method The method to be associated
    * @param string $uri The URL to be accossiated
    * @param string $class The name of the class to be called, it must implement RestAction
    * @param array $mimes Accepted mime types. Optional. Overrides global.
    * @return Rest\Server
    */
    public function addMap($method,$uri,$class,$accept=null) {
        if(isset($this->accept)) $olAccept = $this->accept ;
        if($accept != null) $this->setAccept($accept);
        if(isset($this->accept) and is_array($this->accept)) {
            foreach($this->accept as $accept) {
                foreach($accept[1] as $ext){
                    $this->map[$uri][strtoupper($method)][$ext] = $class ;
                }
            }
        } else {
            $this->map[$uri][strtoupper($method)]['*'] = $class ;
        }
        if($accept != null) {
            $bkpAccept = array();
            if(isset($olAccept)) {
                foreach($olAccept as $acc) {
                    $bkpAccept[] = $acc[0];
                }
            }
            $this->setAccept($bkpAccept);
        }
        return $this ;
    }

    /**
     * Set Accepted mime types globally
     * @param array $mimes
     * @return Rest\Server
     */
    public function setAccept($mimes) {
        $this->accept = array();
        $sys = file_get_contents("/etc/mime.types");
        $lines  = explode("\n",$sys);
        if(!is_array($mimes) || count($mimes) < 1) return $this;
        if($mimes[0] == "*") {
            $this->accept[] = array("*",array("*",""));
        }
        foreach($lines as $line){
            if(strlen($line) < 3 or $line[0] == "#") continue;
            if(preg_match('@^([^\t\s]+)[\t\s]+(.*)$@',$line,$reg)) {
                $mime = $reg[1];
                if(in_array($mime,$mimes)) {
                    $extensions = explode(" ",$reg[2]);
                    $this->accept[] = array($mime,$extensions);
                }
            }
        }
        return $this;
    }

   /**
    * Set the URL to be handle or part of it
    * @param string $value The url
    * @return Rest\Server $this
    */
    public function setQuery($value) {
        $this->getRequest()->setURI($value);
        return $this ;
    }

   /**
    * Get the URL or part of it, depreciated by RestRequest::getURI();
    * @param $k uri part
    * @return string
    **/
    public function getQuery($k=null) { 
        return $this->getRequest()->getURI($k);
    }  

   /**
    * Get the baseurl, based on website location (eg. localhost/website or website.com/);
    * @return string
    **/
    public function getBaseUrl() {
        return $this->baseUrl ;
    }

   /**
    * Get the Response handler object
    * @return Rest\Response
    */
    public function getResponse() {
        return $this->response ;
    }

    /**
     * Get the Request handler object
    * @return Rest\Request
    */
    public function getRequest() {
        return $this->request ;
    }

   /**
    * Get the Authentication handler object
    * @return Rest\Authenticator
    */
    public function getAuthenticator() {
        return $this->authenticator ;
    }

   /**
    * Get the class for specified method and uri
    * @param string $method
    * @param string $uri
    * @param string $extesion optional
    * @return string
    */
    public function getMap($method,$uri,$ext=false) {
        if(count($this->map) < 1) { return false; }
        if($ext === false) $ext = '*';
        else  $uri = substr($uri,0,strlen($uri) - strlen($ext) - 1);
        foreach($this->map as $pattern=>$options) {
            $parts = explode("/",$pattern) ;
            $map = array() ;
            foreach($parts as $part) {
                if(isset($part[0]) && $part[0] == ":" && $part[1] == "?") {
                    $map[] = "?[^/]*";
                } else if(isset($part[0]) && $part[0] == ":") {
                    $map[] = "[^/]+";
                } else {
                    $map[] = $part;
                }
            }
            if(preg_match("%^".implode("/", $map )."$%",$uri)) {
                if(isset($this->map[$pattern][strtoupper($method)][$ext])) {
                    $this->setMatch($parts);
                    return $this->map[$pattern][strtoupper($method)][$ext] ;
                } else if(isset($this->map[$pattern][strtoupper($method)])) {
                    return "\\Rest\\Controller\\NotAcceptable";
                } else if(strtoupper( $method ) == "HEAD"){
                    return "\\Rest\\Controller\\Generic::Head";
                } else if(strtoupper( $method ) == "OPTIONS"){
                    $this->setParameter("opts",implode( array_keys($this->map[$pattern])));
                    return "\\Rest\\Controller\\Generic::Options";
                } else {
                    return "\\Rest\\Controller\\MethodNotAllowed";
                }
            }
        }
        return "\\Rest\\Controller\\NotFound";
    }

    /**
     * Set matched map for request
     * @param array $uri exploded
     * @return Rest\Server
     */
    public function setMatch($map) {
            $this->matched = $map;
            return $this;
    }

    /**
     * Return matched exploded uri for request
     * @return string
     */

    public function getMatch() {
            return $this->matched;
    }

   /**
    * Return last class name from Rest\Server stack trace
    * @return string 
    */
    public function lastClass() {
        $i = count($this->stack);
        return $this->stack[$i - 1];
    }

   /**
    * Run the Server to handle the request and prepare the response
    * @return Rest\Server 
    */
    public function execute($echo=true) {
        if(!$this->getAuthenticator()->tryAuthenticate()) {
            $this->getResponse()->showHeader(); // Call headers, if no yet
            echo $this->getResponse()->getResponse();
            return $this;
        }

        // This is the class name to call
        $response = $this->getMap($this->getRequest()->getMethod(),$this->getQuery(),$this->getRequest()->getExtension()) ;

        if(!is_string($response ) && is_callable($response)) {
            $object = new Controller\Generic($response);
            $method = "execute";
        } else if(is_string($response))  {
            $response = explode("::",$response);
            if(count($response) == 2) {
                $object =  new $response[0];
                $method = $response[1];
            } else {
                $object = new $response[0];
                $method = "execute";
            }
        }

        $this->call($object,$method);
        if(!$this->getResponse()->headerSent()) $this->getResponse()->showHeader(); // Call headers, if no yet
        $r = $this->getResponse()->getResponse();
        if($echo && strlen($r) >= 1) {
            if($this->getRequest()->getMethod() == "GET") {
                $this->getResponse()->addheader("E-Tag: ".md5($r));
            }
            echo $r;
        }
        return $this;
    }

    private function call($object,$method) {             
        $this->stack[] = get_class($object) ;
        if(!($object instanceof Action)) {
            Throw new Exception(get_class($object)." is not a Rest\\Action");
        } else {
            $class = $object->$method($this);
        }

        if($class instanceof Action && get_class($class) != $this->lastClass()) {
            return $this->call($class,"execute"); // May have another class to follow the request
        }
        return $this;
    }

}


