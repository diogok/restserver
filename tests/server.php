<?php

include_once "../Rest/Server.php";

class Foobar implements Rest\Controller {
    public function execute(Rest\Server $rest) {
        if($rest->getRequest()->isPost()) {
            $rest->getResponse()->addHeader("HTTP/1.1 201 Created");
            $rest->getResponse()->setResponse($rest->getBaseUrl()."/server.php?q=/Foo/bar");
        } else {
            $rest->getResponse()->setResponse("Hello World!");
        }
        return $rest;
    }
    public function foo(Rest\Server $rest) {
        if(($name = $rest->getQuery(3)) == null && ($name = $rest->getRequest()->getPost("name")) == null) {
            $name = "RestServer";
        } else {
        }
        $rest->getResponse()->setResponse("Hello , ".ucfirst($name).".");
        return $rest;
    }
    public function bar(Rest\Server $rest) {
        return new Echoer ; 
    }
    public function auth(Rest\Server $rest) {
        $rest->getResponse()->setResponse($rest->getAuthenticator()->getUser());
    }
    public function bench(Rest\Server $rest) {
        $rest->getResponse()->appendResponse("It took ".round(xdebug_time_index(),5)." seconds\n");
        $rest->getResponse()->appendResponse("Used ".round(xdebug_memory_usage()/1024,5)."Kb of Memory\n");
        $rest->getResponse()->appendResponse("Used at peak ".round(xdebug_peak_memory_usage()/1024,5)."Kb of Memory\n");
        return $rest;
    }
}

class Echoer implements Rest\View {
    public function execute(Rest\Server $rest) {
        $r = "Method=".$rest->getRequest()->getMethod()."\n";
        $r .= "URI=".$rest->getRequest()->getRequestURI()."\n";
        foreach($rest->getRequest()->getGET() as $key=>$value) {
            $r .= "GET[".$key."]=".$value."\n";
        }
        foreach($rest->getRequest()->getPOST() as $key=>$value) {
            $r .= "POST[".$key."]=".$value."\n";
        }
        $rest->getResponse()->setResponse(nl2br($r));
        return $rest;
    }
}

$q = (isset($_GET["q"]))?$_GET["q"]:"";

$r = new Rest\Server($q) ;

$r->addMap("GET","/Foo","Foobar");
$r->addMap("POST","/Foo","Foobar");
$r->addMap("GET","/Foo/bar","Foobar::bar");
$r->addMap("GET","/Foo/hello","Foobar::foo");
$r->addMap("POST","/Foo/hello","Foobar::foo");
$r->addMap("GET","/Foo/hello/[\w]*","Foobar::foo");
$r->addMap("GET","/Foo/restricted/basic","Foobar::auth");
$r->addMap("GET","/Foo/restricted/digest","Foobar::auth");
$r->addMap("GET","/Foo/bench","Foobar::bench");
$r->addMap("GET","/Lambda",function($rest) {
    $rest->getResponse()->setResponse("Hello Closure!");
    return $rest;
});
$r->addMap("GET","/hello/:name",function($rest) {
    $rest->getResponse()->setResponse("Hello, ". $rest->getRequest()->getParameter("name")."!");
    return $rest;
});

if($r->getQuery(2) == "restricted") {
    if($r->getQuery(3) == "basic") {
        $r->getAuthenticator()->requireAuthentication(true);
        if($r->getAuthenticator()->getUser() == "joe" && $r->getAuthenticator()->GetPassword() == "123") {
            $r->getAuthenticator()->setAuthenticated(true);
        }
    } else if($r->getQuery(3) == "digest") {
        $r->getAuthenticator()->forceDigest(true);
        $user = $r->getAuthenticator()->getUser();
        $pass = "123"; 
        $r->getAuthenticator()->validate($user,$pass);
    }
}

$r->execute();

?>
