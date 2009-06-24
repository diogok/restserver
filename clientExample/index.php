<?
// dummy example of RestTest
include '../RestClient.class.php';
$google = RestClient::get("http://google.com",array("q"=>"php"));
$twitter = RestClient::post(
                "http://twitter.com/statuses/update.json"
                ,array("status"=>"Working with RestClient from RestServer!")
                ,"username"
                ,"password");
?>
