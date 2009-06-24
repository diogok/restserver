<?

class RestClient {

     private $curl ;
     private $url ;
     private $response ;

     private $method="GET";
     private $params=array();
     private $contentType = null;

     private function __construct() {
         $this->curl = curl_init();
         curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
         curl_setopt($this->curl,CURLOPT_AUTOREFERER,true);
         curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,true);
     }

     public function execute() {
         if($this->method === "POST") {
             curl_setopt($this->curl,CURLOPT_POST,true);
             curl_setopt($this->curl,CURLOPT_POSTFIELDS,$this->params);
         } else {
             curl_setopt($this->curl,CURLOPT_HTTPGET,true);
             if(count($this->params) >= 1) {
                 $this->url .= '?' ;
                 foreach($this->params as $k=>$v) {
                     $this->url .= "&".urlencode($k)."=".urlencode($v);
                 }
             }
         } 
         if($this->contentType != null) {
             curl_setopt($this->curl,CURLOPT_HTTPHEADERS,array("Content-Type: ".$this->contentType));
         }
         curl_setopt($this->curl,CURLOPT_URL,$this->url);
         $this->response = curl_exec($this->curl);
         return $this ;
     }

     public function getResponse() {
         return $this->response ;
     }

     public function setNoFollow() {
         curl_setopt($this->curl,CURLOPT_AUTOREFERER,false);
         curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,false);
         return $this;
     }

     public function close() {
         curl_close($this->curl);
         $this->curl = null ;
         return $this ;
     }

     public function setUrl($url) {
         $this->url = $url; 
         return $this;
     }

     public function setContentType($content) {
         $this->contentType = $content;
         return $this;
     }

     public function setCredentials($user,$pass) {
         if($user != null) {
             curl_setopt($this->curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
             curl_setopt($this->curl,CURLOPT_USERPWD,"{$user}:{$pass}");
         }
         return $this;
     }

     public function setMethod($method) {
         $this->method=$method;
         return $this;
     }

     public function setParameters(array $params) {
         $this->params=$params;
         return $this;
     }

     public static function createClient($url=null) {
         $client = new RestClient ;
         if($url != null) {
             $client->setUrl($url);
         }
         return $client;
     }

     public static function post($url,array $params,$user=null,$pwd=null) {
         return self::createClient($url)
             ->setParameters($params)
             ->setMethod("POST")
             ->setCredentials($user,$pwd)
             ->execute()
             ->close()
             ->getResponse();
     }

     public static function get($url,array $params,$user=null,$pwd=null) {
         $client = self::createClient($url) ;
         $client->setParameters($params);
         $client->setMethod("GET");
         if($user != null) {
             $client->setCredentials($user,$pwd);
         }
         $client->execute();
         $client->close();
         return $client->getResponse() ;
     }

}

?>
