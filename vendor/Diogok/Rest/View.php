<?php

/**
  * Class RestView
  * Interface describe a View for rendering an Response
  */
namespace Diogok\Rest;
interface View extends Action {
	/**
	* Render this view
	* Show($restServer)
	* @param RestServer $restServer
	* @return RestServer 
	* */
	function show(Server $restServer) ;
}
?>
