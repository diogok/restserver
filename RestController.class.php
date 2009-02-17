<?php

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
