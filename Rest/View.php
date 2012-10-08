<?php
namespace Rest;

 /**
  * Class Rest\View
  * Interface describe a View for rendering an Response
  */
interface View extends Action {
	/**
	* Render this view
	* @param Rest\Server $restServer
	* @return Rest\Server 
	*/
	function execute(Server $restServer) ;
}
