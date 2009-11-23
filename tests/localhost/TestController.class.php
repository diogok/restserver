<?php
class TestController implements RestController {

    public function execute(RestServer $rest) {
        $r .= '<p>URI: '.$rest->getRequest()->getRequestURI().'</p>';
        $r .= '<p>Method: '.$rest->getRequest()->getMethod().'</p>';
        $r .= '<p>User: '.$rest->getRequest()->getUser().'</p>';
        $r .= '<p>Password: '.$rest->getRequest()->getPassword().'</p>';
        $r .= '<p>$_GET["key"]: '.$rest->getRequest()->getGET('key').'</p>';
        $r .= '<p>$_POST["key"]: '.$rest->getRequest()->getPOST('key').'</p>';
        $r .= '<p>Body: '.$rest->getRequest()->getBody().'</p>';
        $rest->getResponse()->setResponse($r);
        return $rest;
    }
}
?>
