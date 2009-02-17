<?
class RestResponse {

    private $rest ; 

    private $headers ;
    private $response ;

    function __contruct($rest=null) {
        $this->rest = $rest ;
    }

    public function cleanResponse() {
        $this->response = null ;
        return $this ;
    }
	
	public function addHeader($header) {
        $this->headers[] = $header;
        return $this  ;
	}
	
	public function cleanHeader() {
        $this->headers = Array();
        return $this ;
	}
	
	public function showHeader() {
        if(count($this->headers) >=1) {
            foreach($this->headers as $value) {
                header($value);
            }
        }
        return $this ;
	}
	
	public function headerSent() {
        return headers_sent();
	}
	
	public function setResponse($response) {
        $this->response = $response ;
        return $this ;
	}
	
	public function addResponse($response) {
        $this->response .= $response ;
        return $this ;
	}

    public function getResponse() {
        return $this->response ;
    }

}
?>
