<?php
/*!
 * mongojacket - MongoJacket\DB.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

class DB {
	protected $db=null;
	protected $collections=array();

	public function __construct($connection=null,$dbname=null) {
		$this->db=null;
		$this->collections=array();

		if( is_null($connection)){
			throw new Exception('Conection is missing');
		} else if(is_null($dbname)){ 
			throw new Exception('Database is missing');
		} else {
			try{
				$this->db = $connection->selectDB($dbname);
			} catch(MongoConnectionException $e) {
				throw new Exception('Cannot select database');
			}
		}
		return (is_null($this->db))?null:$this;
	}

	public function collection($collectionname) {
		try{
			if(array_key_exists($collectionname, $this->collections)) {
				if(is_set($collections[$collectionname])){
					return $collections[$collectionname];
				} else {
					$collections[$collectionname]=new Collection($this->db,$collectionname);
					return $collections[$collectionname];
				}
			} else {
				$collections[$collectionname]=new Collection($this->db,$collectionname);
				return $collections[$collectionname];
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return null;
	}
}

?>