<?

    include ("../RestServer.class.php");
    include ("../RestResponse.class.php");
    include ("../RestRequest.class.php");
    include ("../RestAction.class.php");
    include ("../RestView.class.php");
    include ("../RestController.class.php");
        
    class RC implements RestController {
        function execute(RestServer $rest) {
            $rest->getResponse()->setResponse("Yahoo");
            return $rest ;
        }

        function rock(RestServer $rest) {
            $rest->getResponse()->setResponse("We rock!");
            return $rest ;
        }
    }
    
    class RC2 implements RestController {
        function execute(RestServer $rest) {
            return new view2();
        }
    }
    
    class view implements RestView {
      function show(RestServer $rest) {
          $rest->getResponse()->addResponse("Google");
          return $rest ;
      }
    }
    
    class view2 implements RestView {
        function show(RestServer $rest) {
            if($rest->getRequest()->getExtension() == "html") {
                $rest->getResponse()->setResponse("Its in HTML");
            } else {
              $rest->getResponse()->setResponse("Plain Text");
            }
            return $rest ;
        }
    }
    
    $rest = new RestServer();
   
    $rest->getRequest()->setMethod("GET");
    $rest->addMap("POST","/user","RC");
    $rest->addMap("POST","/rock","RC::rock");
    $rest->addMap("POST","/user/([a-zA-Z0-9]*).?[a-zA-Z]{0,5}","RC2");
    $rest->addMap("GET","/user","view");
    $rest->addMap("GET","/rock","RC::rock");
    $rest->addMap("GET","/user/([a-zA-Z0-9]*).?[a-zA-Z]{0,5}","view2");
    
    $q = "/user.html";    
    $rest->setQuery($q);    
    if($rest->getQuery() != "/user.html") {
        echo "Failed at 1";
        return ;
    }
    if($rest->getRequest()->getExtension() != "html") {
        echo "Failed at 2";
        return ;
    }
    
    $q = "/user";
    $rest->setQuery($q);        
    $response = $rest->execute() ;
    if($response != "Google") {
        echo "Failed at 75";
        return ;
    }
    
    $q = "/rock";
    $rest->setQuery($q);        
    $response = $rest->execute() ;
    if($response != "We rock!") {
        echo "Failed at 83";
        return ;
    };
    
    $q = "/user/diogo";
    $rest->setQuery($q);        
    $response = $rest->execute() ;
    if($response != "Plain Text") {
        echo "Failed at 91";
        return ;
    }
    
    
    $q = "/user/diogo.html";
    $rest->setQuery($q);        
    $response = $rest->execute() ;
    if($response != "Its in HTML") {
        echo "Failed at 100";
        return ;
    }
    
    
    $q = "/user";
    $rest->setQuery($q);  
    $rest->getRequest()->setMethod("POST");
    $response = $rest->execute() ;
    if($response != "Yahoo"){
       echo "Failed at 110";
        return ;
    }
    
    $q = "/user/diogo.html";
    $rest->setQuery($q);  
    $rest->getRequest()->setMethod("POST");
    $response = $rest->execute() ;
    if($response != "Its in HTML") {
        echo "Failed at 119";
        return ;
    }
    
    $q = "/user/diogo";
    $rest->setQuery($q);  
    $rest->getRequest()->setMethod("POST");
    $response = $rest->execute() ;
    if($response != "Plain Text") {
        echo "Failed at 128";
        return ;
    }
    
if(!function_exists("xdebug_time_index")) return ;

echo "It took ".round(xdebug_time_index(),5)." seconds \n";
echo "Used ".round(xdebug_memory_usage()/1024,5)."Kb of Memory\n";
echo "Used at peak ".round(xdebug_peak_memory_usage()/1024,5)."Kb of Memory\n";
   
?>
