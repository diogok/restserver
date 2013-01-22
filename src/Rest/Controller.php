<?php
namespace Rest;

 /** 
  * interface Rest\Controller
  * Describe a possible Controller to handle a Request
  */
interface Controller extends Action {

     /**
       * Execute the Default action of this controller
       * @param Rest\Server $restServer
       * @param Rest\Request $request
       * @param Rest\Response $response
       * @return Rest\Action $restVieworController
       */
    function execute(Server $restServer) ;

}

