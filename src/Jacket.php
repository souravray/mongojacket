<?php
/*!
 * mongojacket - MongoJacket\DB.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace MongoJacket;

class Jacket {
	protected $connection;
	protected $dbs;
	public function __construct($serverURI="localhost:27017", $database="test", $usr=null, $pass=null) {
		$this->connection=null;
		$this->dbs=array();

		$connectionString = 'mongodb://';
		if( !is_null($usr) &&  !is_null($pass)) {
			$connectionString .= $usr.":".$pass."@";
		}

		$connectionString .= $serverURI;
		$connectionString .= '/'.$database;
		try {
			$this->connection = new \Mongo($connectionString);
		} catch(MongoConnectionException $e){
			throw new Exception('Database cannot be connected');
		} catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
		return (is_null($this->connection))?null:$this;
	}

	public function db($dbname) {
		try {
			if(isset($this->dbs[$dbname])){
				return $this->dbs[$dbname];
			} else {
				$this->dbs[$dbname]=new DB($this->connection,$dbname);
				return $this->dbs[$dbname];
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return null;
	}

	public function __destruct() {
	}
}

?>