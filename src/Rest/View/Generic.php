<?php
namespace Rest\View;

/**
  * Class GenericView
  * A GenericView representation, throws the result of a script into the Response
  */
class Generic implements \Rest\View {

    protected $file ;
    protected $props ;

    /**
      * Constructor of GenericView
      * @param string $file  The script to be rendered
      * @param mixed $props  Vars to be passed to the script
      */
    function __construct($file=null,$props=null) {
        if($file != null) $this->file = $file ;

        $this->props = array();
        if(is_array($props)) $this->props = $props ;

    }

    /**
      * Render the selected script
      * @param RestServer $rest 
      * @return RestServer
      */
    function execute(\Rest\Server $rest) {
        ob_start();
        extract($rest->getParameters());
        if(is_array($this->props)) extract( $this->props ) ;
        include $this->file ;
        $content = ob_get_clean();
        $rest->getResponse()->setResponse($content);
        return $rest ;
    }

}
