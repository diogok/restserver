<?php

class HomeView implements RestView {
    public function show(RestServer $rest) {
        // Can implement view logic in here(IE: decide template)
        // Lets get the parameter
        $user = $rest->getParameter("user");
        // We could use any templating system
        // As we don't have any, can use the generic view
        $view = new GenericView("templates/home.php",array($user));
        // We set what the response will be
        $rest->getReponse()->setResponse($view);
        // Return rest
        return $rest;
    }
}

?>
