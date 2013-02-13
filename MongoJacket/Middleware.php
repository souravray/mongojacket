<?php
/*!
* mongojacket - MongoJacket\Middleware.php
*
* Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
* License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
*/
namespace MongoJacket;

abstract class Middleware {
    private $parent;
    private $nextMiddelware;

    final public function setParent($parent) {
        $this->parent = $parent;
    }

    final public function getParent() {
        return $this->parent;
    }

    final public function setNext($nextMiddleware) {
        $this->nextMiddelware = $nextMiddleware;
    }

    final public function getNext() {
        return $this->next;
    }

    final public function __get($name)
    {
        switch ($name) {
            case "next":
                return (isset($this->nextMiddelware) && !is_null($this->nextMiddelware))? $this->nextMiddelware : $this->parent;
                break;
            default:
                return isset($this->$name)? $this->$name : NULL;
        }
    }

    abstract public function call();
}
?>