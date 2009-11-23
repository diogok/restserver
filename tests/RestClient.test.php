<?php
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
$url = "http://example";
$user = "user";
$password = "password";

$ex = RestClient::get($url);
$ex = RestClient::get($url,null,$user,$password);
$ex = RestClient::get($url,array('key'=>'value'));
$ex = RestClient::get($url,array('key'=>'value'),$user,$password);

//content post
$ex = RestClient::post($url);
$ex = RestClient::post($url,null,$user,$password);
$ex = RestClient::post($url,array('key'=>'value'));
$ex = RestClient::post($url,array('key'=>'value'),$user,$password); 
$ex = RestClient::post($url,"some text",$user,$password,"text/plain");
$ex = RestClient::post($url,"{ name: 'json'}",$user,$password,"application/json");
$ex = RestClient::post($url,"<xml>Or any thing</xml>",$user,$password,"application/xml");

// General cases
$get = RestClient::get($url,array("q"=>"diogok.json","key"=>"value"),$user,$password);
$post = RestClient::post($url,array("q"=>"diogok.json","key"=>"value"),$user,$password);
$post = RestClient::post($url,"This is my json",$user,$password,"text/plain");
$post = RestClient::post($url."?key=diogok","This is my json",$user,$password,"text/plain");
$put = RestClient::put($url,"This is my json",$user,$password,"text/plain");
$delete = RestClient::delete($url."?key=diogok",array("key"=>"value"),$user,$password);
$http = RestClient::call("OPTIONS",$url."?key=diogok",array("key"=>"values"),$user,$password,"text/plain");
?>
