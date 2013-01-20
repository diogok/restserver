<?php
namespace Rest\View;

/**
  * Class GenericView
  * A GenericView representation, throws the result of a script into the Response
  */
class JSon implements \Rest\View {

    protected $data ;

    /**
      * Constructor of GenericView
      * @param string $file  The script to be rendered
      * @param mixed $props  Vars to be passed to the script
      */
    function __construct($data) {
        $this->data = $data;
    }

    /**
      * Render the selected script
      * @param RestServer $rest 
      * @return RestServer
      */
    function execute(\Rest\Server $rest) {
        $rest->getResponse()->addHeader("Content-Type: application/json");
        $rest->getResponse()->setResponse(json_encode($this->data));
        return $rest ;
    }

}
