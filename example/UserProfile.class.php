<?

class UserProfile implements RestView {
    
    function show(RestServer $rest) {
        $user = $rest->getQuery(2);        
        $rest->getResponse()->setResponse("<h1> User ".$user." </h1>");
        $rest->getResponse()->addResponse("<p> His profile </p>");
        return $rest ;
    }
}


?>
