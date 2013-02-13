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
define("JK_EVNT_INIT", "init");
define("JK_EVNT_FIND", "find");
define("JK_EVNT_SAVE", "save");
define("JK_EVNT_DEL", "delete");

trait Middleware
{
    protected $middlewares=array();

    private $presetEvents=array(
                                    JK_EVNT_INIT,
                                    JK_EVNT_FIND,
                                    JK_EVNT_SAVE,
                                    JK_EVNT_DEL
                                );

    protected $registeredEvents=array();

    private $presetEventSequence=array(
                                        JK_SQNC_PRE,
                                        JK_SQNC_POST
                                    );

    private function validateEvent($event){
        if(is_string($event)){
            if(!empty($this->registeredEvents)){
                $eventSet = array_intersect($this->presetEvents, $this->registeredEvents);
            }else{
                $eventSet=$this->presetEvents;
            }
            return in_array($event, $eventSet)||false;
        }
        return false;
    } 

    private function validateEventSequence($sequence){
        if(is_string($sequence)){
            return in_array($sequence, $this->presetEventSequence)||false;
        }
        return false;
    } 

    protected function addMiddleware($newMiddleware, $event, $sequence) {
        if($this->validateEvent($event) && $this->validateEventSequence($sequence) ) {
            $newMiddleware->setParent($this);
            
            if($newMiddleware instanceof \MongoJacket\DelegationProtocol\Registrable){
                $newMiddleware->register();
            }

            if(!isset($this->middlewares[$event . "-" .$sequence])){
                $this->middlewares[$event . "-" .$sequence]=array();
                $this->middlewares[$event . "-" .$sequence][0]=$newMiddleware;
            } else {
                $this->middlewares[$event . "-" .$sequence][1]->setNext($newMiddleware);
            }
            $this->middlewares[$event . "-" .$sequence][1]=$newMiddleware;
        }
    }

    protected function callMiddleware($event, $sequence) {
        if($this->validateEvent($event) && $this->validateEventSequence($sequence) ) {
            if(isset($this->middlewares[$event . "-" .$sequence])) {
                if( !is_null($this->middlewares[$event . "-" .$sequence][0]) && is_subclass_of($this->middlewares[$event . "-" .$sequence][0] , '\MongoJacket\Middleware') ) {
                    $this->middlewares[$event . "-" .$sequence][0]->call();
                } 
            }
        }
    }
    
    //return call from middleware - can be overridden
    public function call() {
        return;
    }
}

?>