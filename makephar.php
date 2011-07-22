<?php
$phar = new Phar("restserver.phar",0,"restserver.phar");
$phar->buildFromDirectory(dirname(__FILE__),"/\.class\.php$/");
$phar->setDefaultStub("Server.php");

?>
