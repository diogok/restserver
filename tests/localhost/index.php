<?
include '../../RestClient.class.php';

$base = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
var_dump($base);

echo "\n<hr>\n";
$get = RestClient::get($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
$get = RestClient::post($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
$get = RestClient::post($base."/rest.php","This is my json","diogok","123","text/plain");
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
$get = RestClient::post($base."/rest.php?key=diogok","This is my json","diogok","123","text/plain");
var_dump($get->getResponseCOde());
echo $get->getResponse();
?>
