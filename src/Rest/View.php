<?php
namespace Rest;

/**
  * Class Rest_View
  * Interface describe a View for rendering an Response
  */
interface View extends Action {
	/**
	* Render this view
	* Show($restServer)
	* @param RestServer $restServer
	* @return RestServer 
	*/
	function execute(Server $restServer) ;
}
