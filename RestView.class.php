<?php

include_once 'RestAction.class.php';
/**
  * Class RestView
  * Interface describe a View for rendering an Response
  */
interface RestView extends RestAction {
	/**
	* Render this view
	* Show($restServer)
	* @param RestServer $restServer
	* @return RestServer 
	* */
	function show(RestServer $restServer) ;
}
?>
