<?php
/*!
 * mongojacket - MongoJacket\Middleware\Validator.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket\Middleware;

class Validator 
    extends \MongoJacket\Middleware 
    implements \MongoJacket\DelegationProtocol\Registrable {

    public function register() {
        if(method_exists($this->parent, 'bind')){
            $this->parent->bind(
                'validator',
                function($arg=null) {
                    if(!is_null($arg) && count($arg)>=2) {
                        $objectPath=$arg[0];
                        $validation=$arg[1]; 
                        $override=isset($arg[2])?$arg[2]:true;
                        if(!isset($this->validations)) {
                            $this->validations=array();
                        }
                        if(is_string($objectPath)){
                            if(!isset($this->validations[$objectPath]) ||
                                (isset($this->validations[$objectPath]) && $override===true)) {
                                if(is_callable($validation)) {
                                    $this->validations[$objectPath]=array($validation);
                                } else if(is_array($validation)){
                                    $this->validations[$objectPath]=$validation;
                                }
                            } else {
                                if(is_callable($validation)) {
                                    array_push($this->validations[$objectPath], $validation);
                                } else if(is_array($validation)){
                                    $this->validations[$objectPath]=array_merge($this->validations[$objectPath], $validation);
                                }
                            }
                        }
                    }
                });
        }
    }

    private function parseObjectPath($paths, $obj) {
        $path=array_shift($paths);
        try {
            if(is_object($obj) && isset($obj->$path)) {
                if(!is_object($obj->$path) 
                    || !is_array($obj->$path) 
                    || count($paths)==0) {
                    return $obj->$path;
                } else {
                    return $this->parseObjectPath($paths, $obj->$path);
                }
            } else if (is_array($obj) && isset($obj[$path])) {
                if(!is_object($obj[$path]) 
                    || !is_array($obj[$path]) 
                    || count($paths)==0) {
                    return $obj[$path];
                } else {
                    return $this->parseObjectPath($paths, $obj[$path]);
                }
            }
        } catch(\Exception $e) {}

        return null;
    }

    private function callValidator($callables, $object){
        $isValid=true;
        foreach ($callables as $callable){
            $isValid=$isValid && $callable($object);
        }
        return $isValid;
    }

    private function validate() {
        $isValid=true;
        foreach ($this->parent->validations as $objctPath => $callables) {
            $objctPath=trim($objctPath,"\\");
            $paths=explode("\\", $objctPath);
            if(!is_null($this->parent->object)){
                $object=$this->parseObjectPath($paths, $this->parent->object);
                $isValid=$this->callValidator($callables, $object);
            } else if(!is_null($this->parent->objects)){
                foreach ($this->parent->objects as $docObject){
                    $object=$this->parseObjectPath($paths, $docObject);
                    $isValid=$isValid && $this->callValidator($callables, $object);
                }
            }       
        }
        return $isValid;
    } 

    public function call() {
        if(!$this->validate()) {
            throw new \MongoJacket\Exception('Validation Failed');
        }
        $this->next->call();
    }
}

?>