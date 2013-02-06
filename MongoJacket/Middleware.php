<?php
/*!
 * mongojacket - MongoJacket\Middleware.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

abstract class Middleware {
    protected $collection;
    protected $next;

    final public function setCollection($collection) {
        $this->collection = $collection;
    }

    final public function getCollection() {
        return $this->collection;
    }

    final public function setNextMiddleware($nextMiddleware) {
        $this->next = $nextMiddleware;
    }

    final public function getNextMiddleware() {
        return $this->next;
    }

    abstract public function call();
}
?>