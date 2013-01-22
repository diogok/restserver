<?php

class Items implements Rest\Controller {

    public function validate($server) {
        $db = $server->getParameter("db");
        $user =  $server->getRequest()->getParameter("login");
        $login = $server->getAuthenticator()->getUser();
        $exists = $db->prepare("select count(*) from users where login = ?");
        $exists->execute($user);
        if($exists->fetchColumn(0) != 0) return new \Rest\Controller\NotFound;
        if($login != $user) return new \Rest\Controller\Forbidden;
        if(!authenticate($server)) return new \Rest\Controller\NotAuthorized;
        return true;
    }

    public function execute(Rest\Server $server) {
        $valid = $this->validate($server);
        if($valid !== true) return $valid;
    }
} 

?>
