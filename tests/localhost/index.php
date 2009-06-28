<?
include '../../RestClient.class.php';

$base = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
var_dump($base);

echo "\n<hr>\n";
// The GET signature is get(URL,PARAMETERS,USER,PASSWORD), only the URI is mandatory;
$get = RestClient::get($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");
var_dump('$get = RestClient::get($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");');
echo '<br>';
var_dump($get->getResponseCode());
echo $get->getResponse();

echo "\n<hr>\n";
// The POST signature is post(URL,PARAMETERS,USER,PASSWORD,CONTENT-TYPE) , only the URI is mandatory
$get = RestClient::post($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");
var_dump('$get = RestClient::post($base."/rest.php",array("q"=>"diogok.json","key"=>"value"),"diogok","123");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
$get = RestClient::post($base."/rest.php","This is my json","diogok","123","text/plain");
var_dump('$get = RestClient::post($base."/rest.php","This is my json","diogok","123","text/plain");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
$get = RestClient::post($base."/rest.php?key=diogok","This is my json","diogok","123","text/plain");
var_dump('$get = RestClient::post($base."/rest.php?key=diogok","This is my json","diogok","123","text/plain");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
// The PUT signature is put(URL,CONTENT,USER,PASSWORD,CONTENT-TYPE), the URL and CONTENT is mandatory
$get = RestClient::put($base."/rest.php","This is my json","diogok","123","text/plain");
var_dump('$get = RestClient::put($base."/rest.php","This is my json","diogok","123","text/plain");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
// The DELETE signature is delete(URL,PARAMETERS,USER,PASSWORD), the URL is mandatory
$get = RestClient::delete($base."/rest.php?key=diogok",array("key"=>"value"),"diogok","123");
var_dump('$get = RestClient::delete($base."/rest.php?key=diogok",array("key"=>"value"),"diogok","123");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();

echo "\n<hr>\n";
// The custom call signature is call(URL,PARAMETERS,USER,PASSWORD,CONTENT-TYPE), only the URL is mandatory
$get = RestClient::call("OPTIONS",$base."/rest.php?key=diogok",array("key"=>"values"),"diogok","123","text/plain");
var_dump('$get = RestClient::call("OPTIONS",$base."/rest.php?key=diogok",array("key"=>"values"),"diogok","123","text/plain");');
echo '<br>';
var_dump($get->getResponseCOde());
echo $get->getResponse();
?>
