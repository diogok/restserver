<?php

include_once 'RestAction.class.php';
include_once 'RestController.class.php';
include_once 'RestView.class.php';
include_once 'RestRequest.class.php';
include_once 'RestResponse.class.php';
include_once 'RestAuthenticator.class.php';

/**
* Class RestServer 
* Is the front controller for mapping URL to controllers and dealing with Request/Response and Headers
* Made with Restful webservices in mind.
* By Diogo Souza da Silva <manifesto@manifesto.blog.br>
*/
class RestServer {

    private $response ;
    private $request ;
    private $authenticator ;

    private $baseUrl ; 
    private $query ;
    private $qPart;

    private $map ;
    private $params ;
    private $stack ;

    /**
     * Contructor of RestServer
     * @param string $query Optional query to be treat as the URL
     * @return RestServer $rest;
    */
    public function __construct($query=null) {
        $this->request = new RestRequest($this); // Request handler
        $this->response = new RestResponse($this); // Response holder
        $this->authenticator = new RestAuthenticator($this); // Authenticator holder

        if(isset($_SERVER["HTTP_HOST"])) {
            $this->baseUrl = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
        }

        // If will use custom URI or HTTP requested URI
        if($query===null) $this->query = $this->getRequest()->getRequestURI() ;
        else $this->query = $query ;

        $this->getRequest()->setURI($this->query);

        $this->qPart = explode("/",$this->query);
    }

    /**
    * Sets a parameter in a global scope that can be recovered at any request.
    * @param mixed $key The identifier of the parameter
    * @param mixed $value The content of the parameter
    * @return RestServer $this
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
    * @return RestServer $this
    */
    public function addMap($method,$uri,$class) {
        $this->map[$method][$uri] = $class ;
        return $this ;
    }

    /**
    * Set the URL to be handle or part of it
    * @param mixed $value The url
    * @param int $k the part of the url to change
    * @return RestServer $this
    */
    public function setQuery($value,$k=null) {
        if($k !== null){
            $this->qPart[$k]  = $value ;               
        } else {
            $this->query = $value ;
            $this->qPart = explode("/",$value );
            $this->getRequest()->setURI($value);
        }
        return $this ;
    }

    /**
    * Get the URL or part of it, depreciated by RestRequest::getURI();
    * @param $k uri part
    * @return string
    **/
    public function getQuery($k=null) { 
        if($k !== null){
            if(isset($this->qPart[$k])) {
                return $this->qPart[$k];
            } else {
                return '';
            }
        }
        return $this->query ;
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
    * @return RestResponse
    */
    public function getResponse() {
        return $this->response ;
    }

    /**
     * Get the Request handler object
    * @return RestRequest
    */
    public function getRequest() {
        return $this->request ;
    }

    /**
     * Get the Authentication handler object
    * @return RestAuthenticator
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
        foreach($maps as $map=>$class) {
            if(preg_match("%^".$map."$%",$uri) ) {
                return $class ;
            }
        }
        return false ;
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
        if(count($parts = explode("::",$responseClass)) > 1) {
            $responseClass = $parts[0];
            $responseMethod = $parts[1];
        }

        return $this->call(new $responseClass,$responseMethod)->show(); // Call the class and return the response
    }

    private function call($class,$method=null) {             
        $this->stack[] = get_class($class) ;
        if($method != null) {
        } else if($class instanceof RestView) { // If is a view, call Show($restServer)
            $method="show";
        } else if($class instanceof RestController)  {  //If is a controller, call execute($restServer)
            $method="execute";
        } else {
            Throw new Exception(get_class($class)." is not a RestAction");
        }
        $class = $class->$method($this);

        if($class instanceof RestAction 
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
