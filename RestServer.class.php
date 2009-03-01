<?php
            
/**
  * RestServer is the controller for mapping URL to controllers and dealing with Request/Response and Headers
  * made with Restful webservices in mind.
  * By Diogo Souza da Silva <manifesto@manifesto.blog.br>
  */
class RestServer {
	
    private $auth = true;
    private $requireAuth = false ;
	
	private $response ;
    private $request ;
	private $headerOn = false ;
	
	private $baseUrl ; 
	private $query ;
	private $qPart;
	
	private $map ;
    private $params ;
	
    /** You may pass the query/URI for it to handle */
    public function __construct($query=null) {
        $this->request = new RestRequest($this);
        $this->response = new RestResponse($this);
        
	$this->baseUrl = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
				
        if($query===null) $this->query = $this->getRequest()->getRequestURI() ;
        else $this->query = $query ;

        $this->getRequest()->setURI($this->query);

	$this->qPart = explode("/",$this->query);
    }
		
    public function setParameter($key,$value) {
        $this->params[$key] = $value ;
        return $this ;
    }

    public function getParameter($key) {
        return $this->params[$key];
    }
    
    public function setQuery($value,$k=null) {
       if($k !== null){
           $this->qPart[$k]  = $value ;               
       } else {
         $this->query = $value ;
         $this->qPart = explode("/",$value );
       }
       return $this ;
    }
	
    public function addMap($method,$uri,$class) {
       $this->map[$method][$uri] = $class ;
       return $this ;
    }
       
    public function isAuth() {
       return $this->auth ;
    }
        
    public function setAuth($bool) {
      $this->auth = $bool;
       return $this ;
    }
    
    public function getQuery($k=null) { 
       if($k !== null){
          return $this->qPart[$k];
        }
        return $this->query ;
    }  
	
	public function getBaseUrl() {
        return $this->baseUrl ;
	}

	public function getResponse() {
        return $this->response ;
	}
	
    public function getRequest() {
        return $this->request ;
    }

	public function getMap($method,$uri) {
        $maps = $this->map[$method];
        if(count($maps) < 1) return false;
        foreach($maps as $map=>$class) {
            if(preg_match("%^".$map."$%",$uri) ) {
                 return $class ;
            }
        }
        return false ;
	}
	
    private function testAuth() {
        if($this->requireAuth === false) return true;
        if(!$this->auth) { 
            $this->getResponse()->cleanHeader();
            $this->getResponse()->addHeader("HTTP/1.1 401 Unauthorized");
            $this->getResponse()->addHeader('WWW-Authenticate: Basic realm="Restful"');
            $this->getResponse()->setResponse("Unauthorized");                  
            return false ;
        }
        return true ;
    }

    public function requireAuth($bol=null) {
        if($bol !== null) $this->requireAuth = $bol ;
        return $this->requireAuth ;
    }

	public function execute() {
            
        if(!$this->testAuth()) return $this->show();
           
            $responseClass = $this->getMap($this->getRequest()->getMethod(),$this->getQuery()) ;

            if(!$responseClass) {
                $this->getResponse()->cleanHeader();
                $this->getResponse()->addHeader("HTTP/1.1 404 NOT FOUND");
                $this->getResponse()->setResponse("HTTP/1.1 404 NOT FOUND");
                return $this->show();
            }
		
            $parts = explode("::",$responseClass);
            $responseClass = $parts[0];
            $responseMethod = $parts[1];
            $this->call(new $responseClass,$responseMethod);

            return $this->show();		
	}
        
    private function call($class,$method=null) {	                
        if($class instanceof RestView) {
            if($method==null) $method="show";
            $class = $class->$method($this) ;
        } else if($class instanceof RestController)  {
            if($method==null) $method="execute";
            $class = $class->$method($this);
        }

        if($class instanceof RestAction) return $this->call($class);
            
        return $this ;
    }
                
    private function show() {
        $this->testAuth() ;
        if(!$this->getResponse()->headerSent()) {
            $this->getResponse()->showHeader();
        }
        return $this->getResponse()->getResponse() ;
    }

}

?>
