<?php

class UserController implements RestController {
    public function execute(RestServer $rest) {
        return $rest;
    }

    public function list(RestServer $rest) {
        $pdo = new PDO("mysql:localhost","user","pass");
        $q = $pdo->select("select * from users");
        $users= $q->fetchObject();
        $r = "<ul>";
        foreach($users as $user) {
            $r .= "<li><a href='?q=/users/".$user->id."'>".$user->name."</a></li>";
        }
        $r .= "</ul>";
        $rest->getResponse()->addHeader("Content-Type: text/html");
        $rest->getResponse()->setResponse($r); //  Or we return the name 
        return $rest;
    }

    public function view(RestServer $rest) {
        // If an ID is specified
        $id = $rest->getRequest()->getURI(2); // Second part of the URI
        $pdo = new PDO("mysql:localhost","user","pass");
        $q = $pdo->query("select * from users where id = ".$id);
        $users = $q->fetchObject() ;
        if(count($users) < 1) {
            $rest->getResponse()->addHeader("HTTP/1.1 404 NOT FOUND"); // We throw the right header
            $rest->getResponse()->setResponse("User not found"); 
        } else {
			$rest->getResponse()->addHeader("Content-Type: text/plain");
            $rest->getResponse()->setResponse($users[0]->name); //  Or we return the name 
        }
        return $rest;
    }

    public function insert(RestServer $rest) {
        $post = $rest->getRequest()->getPost();
        // Go for the database
        $pdo = new PDO("mysql:localhost","user","pass");
        $pdo->query("insert into users (name) values ('".$post['name']."')");
        $id = $pdo->lastInsertId();
        $rest->getResponse()->addHeader("HTTP/1.1 201 CREATED");
        $rest->getResponse()->addHeader("Content-Type: text/plain");
        $rest->getResponse()->setResponse($id);
        return $rest; 
    }

}

?>
