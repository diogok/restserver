<?

/**
  * A GenericView representation
  */
class GenericView implements RestView {

    protected $file ;
    protected $props ;

    function __construct($file=null,$props=null) {
        if($file != null) $this->file = $file ;
        if($props != null) $this->props = $props ;
    }

    function show(RestServer $rest) {
        ob_start();
        $params = $this->props ;
        include $this->file ;
        $content = ob_get_clean();
        $rest->getResponse()->setResponse($content);
        return $rest ;
    }
}
?>
