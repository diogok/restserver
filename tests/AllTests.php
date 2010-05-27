<?php
include_once 'simpletest/autorun.php';

$dir = opendir(".");
$tests = array();
while($file = readdir($dir)) {
    if(preg_match("/^([\w]*).test.php$/",$file,$reg)) {
        include_once $reg[0];
        $tests[] = $reg[1];
    }
}

class AllTests extends TestSuite {
    function AllTests() {
        global $tests ;
        $this->TestSuite('AllTests');
        foreach($tests as $t) {
            $class = $t."_Tests";
            $this->addTestCase(new $class);
        }
    }
}
?>
