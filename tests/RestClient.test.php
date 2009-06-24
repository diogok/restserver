<?
include '../RestClient.class.php';

$r = RestClient::get("http://www.google.com",array("q"=>"php"));
var_dump($r);

$r = RestClient::post("http://www.google.com",array("q"=>"php"));
var_dump($r);
?>
