<?

class RestClient {

     private $curl ;
     private $url ;
     private $response ="";
     private $headers = array();

     private $method="GET";
     private $params=array();
     private $contentType = null;

     private function __construct() {
         $this->curl = curl_init();
         curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
         curl_setopt($this->curl,CURLOPT_AUTOREFERER,true);
         curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,true);
         curl_setopt($this->curl,CURLOPT_HEADER,true);
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
         $r = curl_exec($this->curl);
         $this->treatResponse($r);
         return $this ;
     }

     public function treatResponse($r) {
        $parts  = explode("\n\r",$r);
        preg_match("@Content-Type: ([a-zA-Z0-9-]+/?[a-zA-Z0-9-]*)@",$parts[0],$reg);
        $this->headers['content-type'] = $reg[1];
        preg_match("@HTTP/1.[0-1] ([0-9]{3}) ([a-zA-Z ]+)@",$parts[0],$reg);
        $this->headers['code'] = $reg[1];
        $this->headers['message'] = $reg[2];
        for($i=1;$i<count($parts);$i++) {
            if($i > 1) {
                $this->response .= "\n\r";
            }
            $this->response .= $parts[$i];
        }
     }

     public function getHeaders() {
        return $this->headers;
     }

     public function getResponse() {
         return $this->response ;
     }

     public function getResponseCode() {
         return $this->headers['code'];
     }
     
     public function getResponseMessage() {
         return $this->headers['message'];
     }

     public function getResponseContentType() {
         return $this->headers['content-type'];
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
             ->close();
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
         return $client ;
     }

}

?>
