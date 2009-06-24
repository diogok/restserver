<?
// dummy example of RestTest
include '../RestClient.class.php';
$twitter = RestClient::post(
                "http://twitter.com/statuses/update.json"
                ,array("status"=>"Working with RestClient from RestServer!")
                ,"username"
                ,"password");

var_dump($twitter->getResponse());
var_dump($twitter->getResponseCode());
var_dump($twitter->getResponseMessage());
var_dump($twitter->getResponseContentType());

?>
