<?php
include_once 'simpletest/autorun.php';


class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('AllTests');
        $dir = opendir(".");
        while($file = readdir($dir)) {
            if(preg_match("/^([\w]*).test.php$/",$file,$reg)) {
                $this->addFile($file);
            }
        }
    }
}
?>
