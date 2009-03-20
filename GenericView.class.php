<?php

/**
  * Class GenericView
  * A GenericView representation, throws the result of a script into the Response
  */
class GenericView implements RestView {

    protected $file ;
    protected $props ;

    /**
      * Constructor of GenericView
      * @param $file=null , The script to be rendered
      * @param $props=null , Vars to be passed to the script
      */
    function __construct($file=null,$props=null) {
        if($file != null) $this->file = $file ;
        if($props != null) $this->props = $props ;
    }

    /**
      * Render the selected script
      * @param RestServer $rest 
      * @return RestServer
      */
    function show(RestServer $rest) {
        ob_start();
        $params = $this->props ;
        include $this->file ;
        $content = ob_get_clean();
        $rest->getResponse()->setResponse($content);
        return $rest ;
    }

}
?>
