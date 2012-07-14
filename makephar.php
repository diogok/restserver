<?php
if(file_exists("restserver.phar")) unlink("restserver.phar");
$phar = new Phar("restserver.phar",0,"restserver.phar");
$phar->buildFromDirectory(dirname(__FILE__)."/Rest");
$stub = <<<EOSTUB
<?php
    Phar::mapPhar("restserver.phar");
    require_once "phar://restserver.phar/Server.php";
    __HALT_COMPILER();
    STUB;
EOSTUB;
$phar->setStub($stub);


?>
