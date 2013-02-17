<?php
/*!
 * mongojacket - MongoJacket\Collection.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

class Collection {
    use \MongoJacket\DelegationProtocol\Middleware;
    use \MongoJacket\DelegationProtocol\Queryable;

    protected $collection=null;
    protected $callables=array();

    public function __construct($db=null,$collectionname=null) {
        if( is_null($db)){
            throw new Exception('Conection is missing');
        } else if(is_null($collectionname)){ 
            throw new Exception('Database is missing');
        } else {
            try{
                $this->collection = $db->selectCollection($collectionname);
            } catch(MongoConnectionException $e) {
                throw new Exception('Cannot select database');
            }
        }
        $this->registeredEvents=array(
            JK_EVNT_FIND,
            JK_EVNT_SAVE,
            JK_EVNT_DEL
            );
        $this->addMiddleware(new \MongoJacket\Middleware\Validator(), JK_EVNT_SAVE, JK_SQNC_PRE);
        return (is_null($this->collection))?null:$this;
    }

    public function find(){
        $this->parseQuery("find", func_get_args());
        $this->setEvent(JK_EVNT_FIND)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function findOne(){
        $this->parseQuery("findOne", func_get_args());
        $this->setEvent(JK_EVNT_FIND)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function batchInsert(){
        $this->parseQuery("batchInsert", func_get_args());
        $this->setEvent(JK_EVNT_SAVE)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function insert(){
        $this->parseQuery("insert", func_get_args());
        $this->setEvent(JK_EVNT_SAVE)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function save(){
        $this->parseQuery("save", func_get_args());
        $this->setEvent(JK_EVNT_SAVE)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function findAndModify(){
        $this->parseQuery("findAndModify", func_get_args());
        $this->setEvent(JK_EVNT_SAVE)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function update(){
        $this->parseQuery("update", func_get_args());
        $this->setEvent(JK_EVNT_SAVE)->setSequence(JK_SQNC_PRE)->callMiddleware();
        return $this->delegatedOrReturned();
    }

    public function call(\Exception $exception=null) {
       try{
            if(is_null($exception)){
                if($this->getSequence()==JK_SQNC_PRE){
                    $query=$this->query;
                    $query($this);
                    $this->setSequence(JK_SQNC_POST)->callMiddleware();
                } else {
                    $this->resetEvent();
                }
            } else {
                $this->exception=$exception;
            }
        } catch(\MongoResultException $e) {
            $this->exception= new Exception('Query failed: '. $e->getMessage());
        } catch(\MongoCursorException $e) {
            $this->exception= new Exception('Cursor error: '. $e->getMessage());
        } catch(\MongoCursorTimeoutException $e) {
            $this->exception= new Exception('Cursor Timeout: '. $e->getMessage());
        } catch (\Exception $e){
            $this->exception=new Exception($e->getMessage());
        }
    }

    protected function delegatedOrReturned(){
        if(is_callable($this->callback)){
            $callback=\Closure::bind($this->callback, $this);
            return $callback($this->result, $this->exception);
        } else {
            return (is_null($this->exception))?$this->result:$this->exception;
        }
    }

    public function bind($functionName, $function) {
        if(!is_callable($function)){
            throw new Exception('Bind methos should be callable');
        } else {
            $this->callables[$functionName]=\Closure::bind($function, $this);
        }
    }

    public function __call($functionName, $arguments) {
        if(isset($this->callables[$functionName])) {
            $this->callables[$functionName]($arguments);            
        } else {
           throw new Exception('Unknown method');
        }
    }
}

?>