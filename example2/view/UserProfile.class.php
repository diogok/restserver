<?php

class UserProfile implements RestView {
    public function show(RestServer $rest ){
        // Lets build the view in here
        $user = $rest->getParameter("user");
        $output = "User:";
        foreach($user as $k=>$v) {
            $output .= "\n".$k."=".$v."";
        }
        $rest->getResponse()->setResponse($output);
        return $rest;
    }
}

?>
