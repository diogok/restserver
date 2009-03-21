<?php

class HomeController implements RestController {
    public function execute(RestServer $rest) {
        // Any controller logic in here
        // For example, could get something from the Model or Repository
        $user = new StdClass ;
        $user->name = "New User";
        // We can store it in the rest server, for the next step
        $rest->setParameter("user",$user);
        return new HomeView ; // We will send the control for the view now
    }
}

?>
