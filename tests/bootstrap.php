<?php

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
include_once("MongoJacket/index.php");

spl_autoload_register('MongoJacket\MongoJacketAutoloader');

//Register tests autoloader
function testAutoLoader( $class )
{
    $classname = rtrim(dirname(__FILE__), '/') . '/' . $class . '.php';
    if ( file_exists($classname) ) {
        include_once $classname;
    }
}
spl_autoload_register('testAutoLoader');


?>