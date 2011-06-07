<?php

include_once "../RestServer.class.php";

class Foobar implements RestController {
    public function execute(RestServer $rest) {
        if($rest->getRequesT()->isPost()) {
            $rest->getResponse()->addHeader("HTTP/1.1 201 Created");
            $rest->getResponse()->setResponse($rest->getBaseUrl()."/server.php?q=/Foo/bar");
        } else {
            $rest->getResponse()->setResponse("Hello World!");
        }
        return $rest;
    }
    public function foo(RestServer $rest) {
        if(($name = $rest->getQuery(3)) == null && ($name = $rest->getRequest()->getPost("name")) == null) {
            $name = "RestServer";
        } else {
        }
        $rest->getResponse()->setResponse("Hello , ".ucfirst($name).".");
        return $rest;
    }
    public function bar(RestServer $rest) {
        return new Echoer ; 
    }
    public function auth(RestServer $rest) {
        $rest->getResponse()->setResponse($rest->getAuthenticator()->getUser());
    }
    public function bench(RestServer $rest) {
        $rest->getResponse()->appendResponse("It took ".round(xdebug_time_index(),5)." seconds\n");
        $rest->getResponse()->appendResponse("Used ".round(xdebug_memory_usage()/1024,5)."Kb of Memory\n");
        $rest->getResponse()->appendResponse("Used at peak ".round(xdebug_peak_memory_usage()/1024,5)."Kb of Memory\n");
        return $rest;
    }
}

class Echoer implements RestView {
    public function show(RestServer $rest) {
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

$r = new RestServer($q) ;

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

echo $r->execute();

?>
