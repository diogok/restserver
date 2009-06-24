<?
// dummy example of RestTest
include '../RestClient.class.php';
$twitter = RestClient::post( // Same for RestClient::get()
                "http://twitter.com/statuses/update.json"
                ,array("status"=>"Working with RestClient from RestServer!") 
                ,"username"
                ,"password");

var_dump($twitter->getResponse());
var_dump($twitter->getResponseCode());
var_dump($twitter->getResponseMessage());
var_dump($twitter->getResponseContentType());

// Other examples
//
$url = "http://example";
$user = "user";
$pwd = "password";

$ex = RestClient::get($url);
$ex = RestClient::get($url,null,$user,$pwd);
$ex = RestClient::get($url,array('key'=>'value'));
$ex = RestClient::get($url,array('key'=>'value'),$user,$pwd);

//content post
$ex = RestClient::post($url);
$ex = RestClient::post($url,null,$user,$pwd);
$ex = RestClient::post($url,array('key'=>'value'));
$ex = RestClient::post($url,array('key'=>'value'),$user,$pwd); 
$ex = RestClient::post($url,"some text",$user,$pwd,"text/plain");
$ex = RestClient::post($url,"{ name: 'json'}",$user,$pwd,"application/json");
$ex = RestClient::post($url,"<xml>Or any thing</xml>",$user,$pwd,"application/xml");
?>
