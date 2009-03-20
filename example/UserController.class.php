<?php
class UserController implements RestController {
    function execute(RestServer $rest) {
       return new GenericView("userForm.php") ;
    }

    function insert($rest) {
        $post = $rest->getRequest()->getPost();
        if($post["user"] == null or $post["pwd"] == null or !preg_match("/^[a-zA-Z0-9]+$/",$post["user"])) {
            $rest->getResponse()->addHeader("HTTP/1.1 406 NOT ACCEPTABLE");
            $rest->getResponse()->addHeader("Location: ?q=/user");
            return ;
        }

        if($rest->getRequest()->getUser() != $post["user"] or $rest->getRequest()->getPassword() != $post["pwd"]) {
            $rest->setAuth(false);
        }
        
        $rest->getResponse()->addHeader("HTTP/1.1 201 Created");
        $rest->getResponse()->setResponse("Congratulations, user <a href='?q=/user/".$post["user"]."'>".$post["user"]."</a> created!");
        return $rest ;
    }
}

?>
