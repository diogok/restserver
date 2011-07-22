<?php

/**
 * Class Client
 * Wraps HTTP calls using cURL, aimed for accessing and testing RESTful webservice. 
 * By Diogo Souza da Silva <manifesto@manifesto.blog.br>
 * Namespace update: zeflasher
 */
namespace Diogok\Rest;
class Client {

     private $curl ;
     private $url ;
     private $response ="";
     private $headers = array();

     private $method="GET";
     private $params=null;
     private $contentType = null;
     private $file =null;

     /**
      * Private Constructor, sets default options
      */
     private function __construct() {
         $this->curl = curl_init();
         curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
         curl_setopt($this->curl,CURLOPT_AUTOREFERER,true); // This make sure will follow redirects
         curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,true); // This too
         curl_setopt($this->curl,CURLOPT_HEADER,true); // This verbose option for extracting the headers
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false); // This make sure it fail on SSL CA
     }

     /**
      * Execute the call to the webservice
      * @return RestClient
      */ 
     public function execute() {
         if($this->contentType != null) {
             curl_setopt($this->curl,CURLOPT_HTTPHEADER,array("Content-Type: ".$this->contentType));
         }
         if($this->method === "POST") {
             curl_setopt($this->curl,CURLOPT_POST,true);
             if(is_array($this->params)) {
                 $params = "";
                 foreach($this->params as $k=>$v) {
                     $params .= "$k=$v&";
                 }
             } else {
                 $params = $this->params ;
             }
             curl_setopt($this->curl,CURLOPT_POSTFIELDS,$params);
         } else if($this->method == "GET"){
             curl_setopt($this->curl,CURLOPT_HTTPGET,true);
             $this->treatURL();
         } else if($this->method === "PUT") {
             curl_setopt($this->curl,CURLOPT_PUT,true);
             $this->treatURL();
             $this->file = tmpFile();
             fwrite($this->file,$this->params);
             fseek($this->file,0);
             curl_setopt($this->curl,CURLOPT_INFILE,$this->file);
             curl_setopt($this->curl,CURLOPT_INFILESIZE,strlen($this->params));
         } else {
             curl_setopt($this->curl,CURLOPT_CUSTOMREQUEST,$this->method);
         }
         curl_setopt($this->curl,CURLOPT_URL,$this->url);
         $r = curl_exec($this->curl);
         $this->treatResponse($r); // Extract the headers and response
         return $this ;
     }

     /**
      * Treats URL
      */
     private function treatURL(){
         if(is_array($this->params) && count($this->params) >= 1) { // Transform parameters in key/value pars in URL
             if(!strpos($this->url,'?')) {
                 $this->url .= '?' ;
             }
             $i = 0;
             foreach($this->params as $k=>$v) {
                 if($i>0) $this->url .= "&";
                 $this->url .= "&";
                 $this->url .= urlencode($k)."=".urlencode($v);
                 $i++;
             }
         }
        return $this->url;
     }

     /*
      * Treats the Response for extracting the Headers and Response
      */ 
     private function treatResponse($r) {
        if($r == null or strlen($r) < 1) {
            return;
        }
        $parts  = explode("\n\r",$r); // HTTP packets define that Headers end in a blank line (\n\r) where starts the body
        while(preg_match('@HTTP/1.[0-1] 100 Continue@',$parts[0]) or preg_match("@Moved@",$parts[0])) {
            // Continue header must be bypass
            for($i=1;$i<count($parts);$i++) {
                $parts[$i - 1] = trim($parts[$i]);
            }
            unset($parts[count($parts) - 1]);
        }
        preg_match("@Content-Type: ([a-zA-Z0-9-]+/?[a-zA-Z0-9-]*)@",$parts[0],$reg);// This extract the content type
        $this->headers['content-type'] = $reg[1];
        preg_match("@HTTP/1.[0-1] ([0-9]{3}) ([a-zA-Z ]+)@",$parts[0],$reg); // This extracts the response header Code and Message
        $this->headers['code'] = $reg[1];
        $this->headers['message'] = $reg[2];
        $this->response = "";
        for($i=1;$i<count($parts);$i++) {//This make sure that exploded response get back togheter
            if(($start = strpos($parts[$i],"\n")) !== false) {
                $parts[$i] = substr($parts[$i],1);
            }
            if($i > 1) {
                $this->response .= "\n\r";
            }
            $this->response .= $parts[$i];
        }
     }

     /*
      * Array of headers
      * @return array
      */
     public function getHeaders() {
        return $this->headers;
     }

     /*
      * Response body
      * @return string
      */ 
     public function getResponse() {
         return $this->response ;
     }

     /*
      * HTTP response code (404,401,200,etc)
      * @return int
      */
     public function getResponseCode() {
         return (int) $this->headers['code'];
     }
     
     /*
      * HTTP response message (Not Found, Continue, etc )
      * @return string
      */
     public function getResponseMessage() {
         return $this->headers['message'];
     }

     /*
      * Content-Type (text/plain, application/xml, etc)
      * @return string
      */
     public function getResponseContentType() {
         return $this->headers['content-type'];
     }

     /**
      * This sets that will not follow redirects
      * @return \Diogok\Rest\Client
      */
     public function setNoFollow() {
         curl_setopt($this->curl,CURLOPT_AUTOREFERER,false);
         curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,false);
         return $this;
     }

     /**
      * This closes the connection and release resources
      * @return \Diogok\Rest\Client
      */
     public function close() {
         curl_close($this->curl);
         $this->curl = null ;
         if($this->file !=null) {
             fclose($this->file);
         }
         return $this ;
     }

    /**
     * Sets the URL to be Called
     * @param string $url
     * @return \Diogok\Rest\Client
     */
     public function setUrl($url) {
         $this->url = $url; 
         return $this;
     }

     /**
      * Set the Content-Type of the request to be send
      * Format like "application/xml" or "text/plain" or other
      * @param string $contentType
      * @return \Diogok\Rest\Client
      */
     public function setContentType($contentType) {
         $this->contentType = $contentType;
         return $this;
     }

     /**
      * Set the Credentials for BASIC Authentication
      * @param string $user
      * @param string $pass
      * @return \Diogok\Rest\Client
      */
     public function setCredentials($user,$pass) {
         if($user != null) {
             curl_setopt($this->curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
             curl_setopt($this->curl,CURLOPT_USERPWD,"{$user}:{$pass}");
         }
         return $this;
     }

     /**
      * Set the Request HTTP Method
      * For now, only accepts GET and POST
      * @param string $method
      * @return \Diogok\Rest\Client
      */
     public function setMethod($method) {
         $this->method=$method;
         return $this;
     }

     /**
      * Set Parameters to be send on the request
      * It can be both a key/value par array (as in array("key"=>"value"))
      * or a string containing the body of the request, like a XML, JSON or other
      * Proper content-type should be set for the body if not a array
      * @param mixed $params
      * @return \Diogok\Rest\Client
      */
     public function setParameters($params) {
         $this->params=$params;
         return $this;
     }

     /**
      * Creates the RESTClient
      * @param string $url
      * @return \Diogok\Rest\Client
      */
     public static function createClient($url=null) {
         $client = new Client ;
         if($url != null) {
             $client->setUrl($url);
         }
         return $client;
     }

     /**
      * Convenience method wrapping a commom POST call
      * @param string $url
      * @param mixed $params
      * @param string $user
      * @param string $password
      * @param string $contentType 
      * @return \Diogok\Rest\Client
      */
     public static function post($url,$params=null,$user=null,$password=null,$contentType="application/x-www-form-urlencoded") {
         return self::call("POST",$url,$params,$user,$password,$contentType);
     }

     /**
      * Convenience method wrapping a commom PUT call
      * @param string $url
      * @param string $body 
      * @param string $user
      * @param string $password
      * @param string $contentType 
      * @return \Diogok\Rest\Client
      */
     public static function put($url,$body,$user=null,$password=null,$contentType=null) {
         return self::call("PUT",$url,$body,$user,$password,$contentType);
     }

     /**
      * Convenience method wrapping a commom GET call
      * @param string $url
      * @param array params
      * @param string $user
      * @param string $password
      * @return \Diogok\Rest\Client
      */
     public static function get($url,array $params=null,$user=null,$password=null) {
         return self::call("GET",$url,$params,$user,$password);
     }

     /**
      * Convenience method wrapping a commom delete call
      * @param string $url
      * @param array params
      * @param string $user
      * @param string $password
      * @return \Diogok\Rest\Client
      */
     public static function delete($url,array $params=null,$user=null,$password=null) {
         return self::call("DELETE",$url,$params,$user,$password);
     }

     /**
      * Convenience method wrapping a commom custom call
      * @param string $method
      * @param string $url
      * @param string $body 
      * @param string $user
      * @param string $password
      * @param string $contentType 
      * @return \Diogok\Rest\Client
      */
     public static function call($method,$url,$body,$user=null,$password=null,$contentType=null) {
         return self::createClient($url)
             ->setParameters($body)
             ->setMethod($method)
             ->setCredentials($user,$password)
             ->setContentType($contentType)
             ->execute()
             ->close();
     }
}

?>
