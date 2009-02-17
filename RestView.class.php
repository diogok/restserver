<?php

interface RestView extends RestAction {
    /**
       * Render this view
       * Show($restServer)
       * @param RestServer $restServer
       * @return string HTML
       *
     * */
    function show(RestServer $restServer) ;
}
?>
