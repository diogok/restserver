<?php
/**
  * RestResponse hold the response in a RestServer
  */
class RestResponse {

    private $rest ; 

    private $headers ;
    private $response ;
    private $relm = "RESTful";
    private $useDigest = false;

    /** 
    * Constructor of RestServer
    * @param RestServer $rest
    */
    public function __contruct(RestServer $rest=null) {
        $this->rest = $rest ;
    }


    /**
    * Adds a header to the response
    * @param string $header
    * @return RestResponse
    */
    public function addHeader($header) {
        $this->headers[] = $header;
        return $this  ;
    }

    /**
    * Clean the headers set on the response
    * @return RestResponse
    */
    public function cleanHeader() {
        $this->headers = Array();
        return $this ;
    }

    /**
    * Show the headers
    * @return RestResponse
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
    * @return RestResponse
    */
    public function setResponse($response) {
        $this->response = $response ;
        return $this ;
    }

    /**
    * Set the response to null
    * @return RestResponse
    */
    public function cleanResponse() {
        $this->response = null ;
        return $this ;
    }

    /**
    * Add a string to the response, only work if response is a string
    * @param string $response
    * @return RestResponse
    */
    public function appendResponse($response) {
        return $this->addResponse($response);
    }

    /**
    * Add a string to the response, only work if response is a string
    * @param string $response
    * @return RestResponse
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
