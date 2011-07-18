<?php

/**
 * Class GenericView
 * A GenericView representation, throws the result of a script into the Response
 * Namespace update: zeflasher`
 */
namespace Diogok\Rest;
class GenericView implements View {

    protected $file ;
    protected $props ;

    /**
      * Constructor of GenericView
      * @param string $file  The script to be rendered
      * @param mixed $props  Vars to be passed to the script
      */
    function __construct($file=null,$props=null) {
        if($file != null) $this->file = $file ;
        if($props != null) $this->props = $props ;
    }

    /**
      * Render the selected script
      * @param \Diogok\Rest\Server $rest
      * @return \Diogok\Rest\Server
      */
    function show(Server $rest) {
        ob_start();
        $params = $this->props ;
        include $this->file ;
        $content = ob_get_clean();
        $rest->getResponse()->setResponse($content);
        return $rest ;
    }

}
?>
