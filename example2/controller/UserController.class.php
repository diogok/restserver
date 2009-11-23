<?php

class UserController implements RestController {
    public function execute(RestServer $rest) {
        // defaul method
        return $rest;
    }

    public function profile(RestServer $rest) {
        // If an ID is specified
        $id = $rest->getRequest()->getURI(2); // Second part of the URI
        // Search on a database, or whatever
        $users = array() ;
        // Lets say no user was found
        if(count($users) < 1) {
            $rest->getResponse()->addHeader("HTTP/1.1 404 NOT FOUND"); // We throw the right header
            $rest->getResponse()->setResponse("User not found"); 
        } else {
            $user = new StdClass ;
            $user->name = "John";
            $rest->setParameter("user",$user);
            $rest->getResponse()->setResponse(new UserProfile); //  Or we go to the view
        }
        return $rest;
    }

    public function insert(RestServer $rest) {
        // Insert logic in here
        $post = $rest->getRequest()->getPost();
        $user = new StdClass ;
        foreach($post as $k=>$v) {
            $user->$k = $v;
        }
        // Go for the database
        // You can test if creation worked
        $insertWorked = true ;
        if($insertWorked) {
			$rest->getResponse()->addHeader("HTTP/1.1 201 CREATED");
			$rest->getResponse()->addHeader("Content-Type: text/xml");
            $rest->getResponse()->setResponse($xml_of_the_user);
        } else {
			$rest->getResponse()->addHeader("HTTP/1.1 406 NOT ACCEPTABLE");
			$rest->getResponse()->addHeader("Content-Type: text/plain");
            $rest->getResponse()->setResponse("Invalid input");
        }
        return $rest; 
    }

    public function update(RestServer $rest) {
        // Lets say we need to authenticate this user first

        $login = $rest->getRequest()->getUser();
        $pwd = $rest->getRequest()->getPassword();

        if($login != "John" && $pwd != "Doe") {  // If no auth
            $rest->unAuth(); // Let rest request it
            return $rest;
        }
        // Remenber that this can come from PUT or POST
        if($rest->getRequest()->isPUT()) {
            $files = $rest->getRequest()->getPUT();
            // Update logic in here;
        } else { // If isPOST()
            $post = $rest->GetRequest()->getPOST();
            // Update logic in here;
        }
        // Keep the good job
        return $rest;
    }
}

?>
