<?php
/**
* Class Server
* Is the front controller for mapping URL to controllers and dealing with Request/Response and Headers
* Made with Restful webservices in mind.
* By Diogo Souza da Silva <manifesto@manifesto.blog.br>
*/
namespace Diogok\Rest;
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

    /**
     * Contructor of RestServer
     * @param string $query Optional query to be treat as the URL
     * @return RestServer $rest;
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
    }

    /**
    * Sets a parameter in a global scope that can be recovered at any request.
    * @param mixed $key The identifier of the parameter
    * @param mixed $value The content of the parameter
    * @return \Diogok\Rest\Server
    */
    public function setParameter($key,$value) {
        $this->params[$key] = $value ;
        return $this ;
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
    * @return \Diogok\Rest\Server
    */
    public function addMap($method,$uri,$class) {
        $this->map[$method][$uri] = $class ;
        return $this ;
    }

    /**
    * Set the URL to be handle or part of it
    * @param mixed $value The url
    * @param int $k the part of the url to change
    * @return \Diogok\Rest\Server
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
    * @return \Diogok\Rest\Response
    */
    public function getResponse() {
        return $this->response ;
    }

    /**
     * Get the Request handler object
    * @return \Diogok\Rest\Request
    */
    public function getRequest() {
        return $this->request ;
    }

    /**
     * Get the Authentication handler object
    * @return \Diogok\Rest\Authenticator
    */
    public function getAuthenticator() {
        return $this->authenticator ;
    }

    /**
    * Get the class for specified method and uri
    * @param string $method
    * @param string $uri
    * @return string
    */
    public function getMap($method,$uri) {
        $maps = $this->map[$method];
        if(count($maps) < 1) { return false; }
        foreach($maps as $pattern=>$class) {
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
            if(preg_match("%^".implode("/", $map )."$%",$uri) ) {
                $this->setMatch($parts);
                return $class ;
            }
        }
        return false ;
    }

    /**
     * Set matched pattern
     * @param array $map
     * @return \Diogok\Rest\Server
     */
    public function setMatch($map) {
        $this->matched = $map;
        return $this;
    }

    /**
     * Get matched pattern
     * @return array
     */
    public function getMatch() {
        return $this->matched;
    }

    /**
    * Return last class name from RestServer stack trace
    * @return string 
    */
    public function lastClass() {
        $i = count($this->stack);
        return $this->stack[$i - 1];
    }

    /**
    * Run the Server to handle the request and prepare the response
    * @return string $responseContent
    */
    public function execute() {
        if(!$this->getAuthenticator()->tryAuthenticate()) {
            return $this->show(); // If auth is required and its not ok, response is 401
        }

        // This is the class name to call
        $responseClass = $this->getMap($this->getRequest()->getMethod(),$this->getQuery()) ;
        $responseMethod = null;

        if(!$responseClass) { // If no class was found, response is 404
            $this->getResponse()->cleanHeader();
            $this->getResponse()->addHeader("HTTP/1.1 404 Not found");
            $this->getResponse()->setResponse("HTTP/1.1 404 NOT FOUND");
            return $this->show();
        }

        // In case a specific method should be called
        if(is_string($responseClass) && count($parts = explode("::",$responseClass)) > 1) {
            $responseClass = $parts[0];
            $responseMethod = $parts[1];
        }

        $responseObject = new \stdClass() ;

        if(is_callable($responseClass)) {
            $responseObject = $responseClass; 
        } else {
            $responseObject = new $responseClass;
        }

        return $this->call($responseObject,$responseMethod)->show(); // Call the class and return the response
    }

    private function call($class,$method=null) {             
        $this->stack[] = get_class($class) ;
        if(is_callable($class)) {
            $class = $class($this);
        } else if($method != null) {
            $class = $class->$method($this);
        } else if($class instanceof View) { // If is a view, call Show($restServer)
            $class = $class->show($this);
        } else if($class instanceof Controller)  {  //If is a controller, call execute($restServer)
            $class = $class->execute($this);
        } else {
            Throw new \Exception(get_class($class)." is not a RestAction");
        }

        if($class instanceof Action
            && get_class($class) != $this->lastClass() ) {
            return $this->call($class); // May have another class to follow the request
        }

        return $this ;
    }

    private function show() {
        if(!$this->getResponse()->headerSent()) {
            $this->getResponse()->showHeader(); // Call headers, if no yet
        }
        return $this->getResponse()->getResponse() ; // Return response content;
    }
}

?>
