<?php
/**
 * RestResponse hold the response in a Server
 * Namespace update: zeflasher
 */
namespace Diogok\Rest;
class Response {

    private $rest ; 

    private $headers ;
    private $response ;
    private $relm = "RESTful";
    private $useDigest = false;

    /** 
    * Constructor of RestServer
    * @param RestServer $rest
    */
    public function __contruct(Server $rest=null) {
        $this->rest = $rest ;
    }


    /**
    * Adds a header to the response
    * @param string $header
    * @return \Diogok\Rest\Response
    */
    public function addHeader($header) {
        $this->headers[] = $header;
        return $this  ;
    }

    /**
    * Clean the headers set on the response
    * @return \Diogok\Rest\Response
    */
    public function cleanHeader() {
        $this->headers = Array();
        return $this ;
    }

    /**
    * Show the headers
    * @return \Diogok\Rest\Response
    */
    public function showHeader() {
        if(count($this->headers) >=1) {
            foreach($this->headers as $value) {
                header($value);
            }
        }
        return $this ;
    }

    /**
    * Check if headers were sent
    * @return bool
    */
    public function headerSent() {
        return headers_sent();
    }

    /**
    * Set the response
    * @param mixed $response
    * @return \Diogok\Rest\Response
    */
    public function setResponse($response) {
        $this->response = $response ;
        return $this ;
    }

    /**
    * Sends the partial response already, skip buffering, good for big responses
    * @param mixed $response
    * @return \Diogok\Rest\Response
    */
    public function sendResponse($response) {
        if(!$this->headerSent()) $this->showHeader();
        echo $response ;
        return $this;
    }

    /**
    * Set the response to null
    * @return \Diogok\Rest\Response
    */
    public function cleanResponse() {
        $this->response = null ;
        return $this ;
    }

    /**
    * Add a string to the response, only work if response is a string
    * @param string $response
    * @return \Diogok\Rest\Response
    */
    public function appendResponse($response) {
        return $this->addResponse($response);
    }

    /**
    * Add a string to the response, only work if response is a string
    * @param string $response
    * @return \Diogok\Rest\Response
    */
    public function addResponse($response) {
        $this->response .= $response ;
        return $this ;
    }

    /**
    * Return the reponse set
    * @return mixed $response;
    */
    public function getResponse() {
        return $this->response ;
    }

}
?>
