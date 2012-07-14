<?php

include '../restserver.phar';

$rest = new Rest\Server($_GET["q"]);

$rest->setParameter("salt","&dcalsd-09o");

$create = (!file_Exists("data.db"));
if($create) touch("data.db");
$db = new PDO("sqlite:data.db");
if($create) $db->query(file_get_contents("schema.sql"));
$rest->setParameter("db",$db);

function authenticate($rest) {
    $db = $rest->getParameter("db");
    $salt = $rest->getParameter("salt");

    $login = $rest->getAuthenticator()->getUser();
    $pass  = $rest->getAuthenticator()->getPassword();
    $auth = $db->prepare("select password from users where login = ?");
    $auth->execute(array($login));
    if(($a = $auth->fetchColumn(0)) != md5($salt.$pass)) {
        return false;
    } else {
        return true;
    }
}

$rest->addMap("GET","/",function($rest) {
    return new Rest\View\Generic("ui.php");
});

$rest->addMap("POST","/users",function($rest) {
    $db = $rest->getParameter("db");
    $salt = $rest->getParameter("salt");

    $user = json_decode($rest->getRequest()->getBody());

    $unique = $db->prepare("select count(*) from users where login = ?");
    $unique->execute(array( $user->login ));
    if($unique->fetchColumn(0) != 0) new \Rest\Controller\BadRequest;

    $insert = $db->prepare("insert into users (login, password, name) values (?,?,?)");
    $insert->execute(array($user->login,md5($salt.$user->password),$user->name));

    $rest->getResponse()->setResponse(json_encode($user));
    return new \Rest\Controller\Created;
});

$rest->addMap("GET","/users/:login",function($rest) {
    $db = $rest->getParameter("db");
    $user =  $rest->getRequest()->getParameter("login");
    if(!authenticate($rest)) return new \Rest\Controller\NotAuthorized;

    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute(array($user));
    if($exists->fetchColumn(0) == 0) return new \Rest\Controller\NotFound;

    $login = $rest->getAuthenticator()->getUser();
    if($login != $user) return new \Rest\Controller\Forbidden;

    $select = $db->prepare("select login, name from users where login = ?");
    $select->execute(array($user));
    $user = $select->fetchObject();
    return new \Rest\View\JSon($user);
});

$rest->addMap("PUT","/users/:login",function($rest) {
    $db = $rest->getParameter("db");
    $salt = $rest->getParameter("salt");

    $user = json_decode($rest->getRequest()->getBody());
    if(!authenticate($rest)) return new \Rest\Controller\NotAuthorized;

    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute($user->login);
    if($exists->fetchColumn(0) != 0) return new \Rest\Controller\NotFound;

    $login = $rest->getAuthenticator()->getUser();
    if($login != $user->login) return new \Rest\Controller\Forbidden;

    $update = $db->prepare("update users set password = ?, name = ? where login = ?");
    $update->execute(array(md5($salt.$user->password),$user->name,$user->login));
    return new \Rest\View\JSon($user);
});

$rest->addMap("DELETE","/users/:login",function($rest) {
    $db = $rest->getParameter("db");
    $user =  $rest->getRequest()->getParameter("login");
    if(!authenticate($rest)) return new \Rest\Controller\NotAuthorized;

    $exists = $db->prepare("select count(*) from users where login = ?");
    $exists->execute($user);
    if($exists->fetchColumn(0) != 0) return new \Rest\Controller\NotFound;

    $login = $rest->getAuthenticator()->getUser();
    if($login != $user) return new \Rest\Controller\Forbidden;

    $delete = $db->prepare("delete from users where login = ?");
    $delete->execute(array($user));
    return new \Rest\View\JSon($user);
});

$rest->addMap("GET","/:login/items",function($rest) {
});

$rest->addMap("POST","/:login/items",function($rest) {
});

$rest->addMap("PUT","/:login/items/:key",function($rest) {
});

$rest->addMap("DELETE","/:login/items/:key",function($rest) {
});

$rest->execute();

?>
