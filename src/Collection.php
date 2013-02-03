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

	public function find($option="{}"){
		$cursor = $this->collection->find();
		for($i=0; $i<$cursor->count();$i++){
			echo "<code>";
			echo json_encode(arrayToObject($cursor->getNext()));
			echo "</code><br/>";
		}
	}
}

?>