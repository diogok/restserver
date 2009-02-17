<?
class HomeController implements RestController {
    
    function execute(RestServer $rest) {
        return new GenericView("home.php");
    }
}
