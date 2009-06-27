<?
// This is a dummy client to test and consume the service
include '../RestClient.class.php';


$base = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);// this is to know here URL;

$userList = RestClient::get($base."/index.php?q=/users");
$code = $userList->getResponseCode(); // If the response was success will be 200
if($code == 200) {
    echo $userList->getResponse(); // This is the response message
}

$userInsert = RestClient::post($base."/index.php?q=/users",array("name"=>"diogo"));
$code = $userInsert->getResponseCode(); // If the response was success will be 201
if($code == 201) { // 201 mean created
    echo $userInsert->getResponse(); // This is the response message, in this case the id
} else {
    echo "fail in insert: ".$userInsert->getResponseMessage()." -> ".$userInsert->getResponse();
}

$id = $code->getResponse();
$userView = RestClient::get($base."/index.php?q=/users/".$id);
$code = $userView->getResponseCode();
if($code == 200) {
    echo $userView->getReponse();
} else if($code == 404) {
    echo "Fail, no such user";
}
?>
