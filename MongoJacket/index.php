<?php
/*!
 * mongojacket - MongoJacket\index.js
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

// PSR-0 Autoloader
function MongoJacketAutoloader ($classname) {
    $classname = ltrim($classname, "\\");
   if( preg_match('/^MongoJacket\\\\/U', $classname)){
	    preg_match('/^(.+)?([^\\\\]+)$/U', $classname, $match);
	    $classname =  str_replace(array("\\", "_"), "/", $match[1]). str_replace(array("\\", "_"), "/", $match[2]) . ".php";
	    include_once $classname;
	}
}

if (!extension_loaded('mongo')){
	throw new Exception('Mongo driver is missing');
}

function arrayToObject($d) {
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		// Return object
		return $d;
	}
}

?>