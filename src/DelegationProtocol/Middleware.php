<?php
/*!
 * mongojacket - MongoJacket\DelegationProtocol\Middleware.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket\DelegationProtocol;

define("JK_SQNC_PRE", "pre");
define("JK_SQNC_POST", "post");
define("JK_EVNT_FIND", "find");
define("JK_EVNT_SAVE", "save");
define("JK_EVNT_DEL", "delete");

trait Middleware
{
	protected $middlewares=array();

	protected function addMiddleware($newMiddleware, $event, $type) {
		$newMiddleware->setCollection($this);
		if(!isset($this->middlewares[$event . "-" .$type])){
			$this->middlewares[$event . "-" .$type]=array();
			$this->middlewares[$event . "-" .$type][0]=$newMiddleware;
		} else {
        	$this->middlewares[$event . "-" .$type][1]->setNextMiddleware($newMiddleware);
        }
        $this->middlewares[$event . "-" .$type][1]=$newMiddleware;
	}

	protected function callMiddleware($event, $type) {
		if(isset($this->middlewares[$event . "-" .$type])) {
			if( !is_null($this->middlewares[$event . "-" .$type][0]) && is_subclass_of($this->middlewares[$event . "-" .$type][0] , '\MongoJacket\Middleware') ) {
				$this->middlewares[$event . "-" .$type][1]->setNextMiddleware($this);
				$this->middlewares[$event . "-" .$type][0]->call();
			} 
		}
	}

	final public function call() {

	}
}

?>