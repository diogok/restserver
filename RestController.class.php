<?php

include_once 'RestAction.class.php';
/** Class RestController
  * Describe a possible Controller to handle a Request
  */
interface RestController extends RestAction {
     /**
       * Execute the Default action of this controller
       * @param RestServer $restServer
       * @return RestAction $restVieworController
       *
     * */
    function execute(RestServer $restServer) ;
}

?>
