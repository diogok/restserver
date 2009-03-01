<?
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
	
    public function __construct($rest=null) {

        $this->rest = $rest ;
            
		$this->requestMethod = $_SERVER["REQUEST_METHOD"];
		$this->requestURI = $_SERVER["REQUEST_URI"];
        $this->URIParts = explode("/",$this->requestURI);
                
        $this->authData = $_SERVER['PHP_AUTH_DIGEST'] ;                
        $this->user = $_SERVER["PHP_AUTH_USER"];
        $this->pwd = $_SERVER["PHP_AUTH_PW"];
		
		$this->get = $_GET ;
		$this->post = $_POST ;
        $this->files = $_FILES ;
		
    }
 
    public function isGet() {
        if($this->requestMethod == "GET") {
            return true ;
        }
        return false;
    }

    public function isPost() {
       if($this->requestMethod == "POST") {
           return true ;
       }
       return false;
    }

    public function isPut() {
       if($this->requestMethod == "PUT") {
           return true ;
       }
       return false;
    }

    public function isDelete() {
       if($this->requestMethod == "DELETE") {
           return true ;
       }
       return false;
    }
	
    public function getGet($k=null) {
        if($k==null) return $this->get ;
        else return $this->get[$k] ;
    }

    public function getPost($k=null) {
        if($k==null) return $this->post ;
        else return $this->post[$k] ;
    }

    public function getFiles($k=null) {
        if($k==null) return $this->files ;
        else return $this->files[$k];
    }

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

   public function getAuthData() {
      return $this->authData;
   }
   
   public function getUser() {
       return $this->user;
   }
        
   public function getPassword() {
       return $this->pwd ;
   }
    
   public function getMethod() {
      return $this->requestMethod ;
   }
        
   public function setMethod($m) {
       $this->requestMethod = $m ;
   }
	
    public function getRequestURI() {
        return $this->requestURI ;
    }

    public function getURIpart($i) {
        return $this->URIParts[$i];
    }

    public function getURI($i=null) {
        if($i !== null) return $this->getURIpart($i);
        return $this->requestURI ;
    }

    public function setURI($url) {
		$this->requestURI = $url;
        $this->URIParts = explode("/",$this->requestURI);
        return $this ;
    }

    public function getExtension() {
       preg_match('@\.([a-zA-Z0-9]{1,5})$@',$this->rest->getQuery(),$reg);
       return $reg[1];
    }
	
	public function acceptMime($mime) {
        if(strpos($_SERVER["HTTP_ACCEPT"],$mime) > 0) {
            return true ;
        } else {
            return false ;
        }
	}
	
}
?>
