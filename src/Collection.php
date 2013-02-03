<?php
/*!
 * mongojacket - MongoJacket\Collection.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

class Collection {
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
		return (is_null($this->collection))?null:$this;
	}

	public function find($option=array(), $callback=null){
		$documents = array();
		try{
			$cursor = $this->collection->find($option);
			for($i=0; $i<$cursor->count();$i++){
				array_push( $documents, arrayToObject($cursor->getNext()));
			}
		} catch(MongoResultException $e) {
			throw new Exception('Query failed: '. $e->getMessage());
		} catch(MongoCursorException $e) {
			throw new Exception('Cursor error: '. $e->getMessage());
		} catch(MongoCursorTimeoutException $e) {
			throw new Exception('Cursor Timeout: '. $e->getMessage());
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		if(is_callable($callback)){
			\Closure::bind($callback, $this);
			return $callback($documents);
		} else {
			return $documents;
		}
	}

	public function bind($functionName, $function){
		if(!is_callable($function)){
			throw new Exception('Bind methos should be callable');
		} else {
			$this->callables[$functionName]=\Closure::bind($function, $this);
		}
	}

	public function __call($functionName, $arguments){
		if(!isset($this->callables[$functionName])){
			throw new Exception('Unknown method');
		} else {
			$this->callables[$functionName]($arguments);
		}
	}
}

?>