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

trait Middleware {
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

    private $currentEvent=null; 
    private $currentSequence=null;

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

    final protected function setEvent($event) {
        if($this->validateEvent($event)) {
         $this->currentEvent=$event;
        }
        return $this; //Return current instance for Chainable method call 
    }

    final protected function setSequence($sequence) {
        if($this->validateEventSequence($sequence)) {
            $this->currentSequence=$sequence;
        }
        return $this; //Return current instance for Chainable method call 
    }

    final protected function resetEvent(){
        $this->currentEvent=null;
        $this->currentSequence=null;
    }

    final protected function addMiddleware($newMiddleware, $event, $sequence) {
        if($this->validateEvent($event) 
            && $this->validateEventSequence($sequence)) {
            $newMiddleware->setParent($this);
            if($newMiddleware instanceof \MongoJacket\DelegationProtocol\Registrable){
                $newMiddleware->register();
            }
            $eventKey=$event . "-" .$sequence;
            if(!isset($this->middlewares[$eventKey])){
                $this->middlewares[$eventKey]=array();
                $this->middlewares[$eventKey][0]=$newMiddleware;
            } else {
                $this->middlewares[$eventKey][1]->setNext($newMiddleware);
            }
            $this->middlewares[$eventKey][1]=$newMiddleware;
        }
    }

    final protected function callMiddleware() {
        if(!is_null($this->currentEvent) 
            && !is_null($this->currentSequence)) {
            $eventKey=$this->currentEvent . "-" .$this->currentSequence;
            if(isset($this->middlewares[$eventKey])
                && isset($this->middlewares[$eventKey][0])
                && is_subclass_of($this->middlewares[$eventKey][0] , '\MongoJacket\Middleware')
                ) {
                $this->middlewares[$eventKey][0]->call();
            } else {
                $this->call();
            }
        } else {
            $this->call();
        }
    }
    
    // This method is to accept return call from middleware
    // by default this is a Sentinel method call
    // can be overridden in implementation class
    public function call() {
        $this->resetEvent();
        return;
    }
}

?>