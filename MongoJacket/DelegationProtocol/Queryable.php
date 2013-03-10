<?php
/*!
 * mongojacket - MongoJacket\DelegationProtocol\Quarable.php
 *
 * Copyright(c) 2013 Sourav Ray <me[at]raysourav[dot]com>
 * License - BSD 2-Clause (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace MongoJacket\DelegationProtocol;

trait Queryable {
    protected $query=null;
    protected $callback=null;
    public $objects=null;
    public $object=null;
    public $criteria=null;
    public $limit=0;
    public $skip=0;
    public $fields=null;
    public $result=null;
    protected $exception=null;
    public $options=array();

    final protected function parseQuery($method=null, $arguments=array()){
        $this->resetGlobalQuery();
        if(!is_null($method)) {
            if(count($arguments) >1 
                && is_callable($arguments[count($arguments)-1])
            ) {
                $this->callback=array_pop($arguments);
            }
            switch ($method){
                case "batchInsert":
                    $this->objects=$this->unshiftStack($arguments);
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->batchInsert($obj->objects, $obj->options);
                        };
                    break;
                case "insert":
                    $this->object=$this->unshiftStack($arguments);
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->insert($obj->object, $obj->options);
                        };
                    break;
                case "save":
                    $this->object=$this->unshiftStack($arguments);
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->save($obj->object, $obj->options);
                        };
                    break;
                case "find":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->fields=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {                       
                            $obj->objects=$obj->collection->find($obj->criteria, $obj->fields);
                            $obj->result=&$obj->objects;
                        };
                    break;
                case "findOne":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->fields=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->object=$obj->collection->findOne($obj->criteria, $obj->fields);
                            $obj->result=&$obj->object;
                        };
                    break;
                case "findAndModify":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->object=$this->unshiftStack($arguments,array());
                    $this->fields=$this->unshiftStack($arguments,array());
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->object=$obj->collection->findAndModify($obj->criteria, $this->object, $obj->fields, $this->options);
                            $obj->result=&$obj->object;
                        };
                    break;
                case "update":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->object=$this->unshiftStack($arguments,array());
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->findAndModify($obj->criteria, $this->object, $this->options);
                        };
                    break;
                case "remove":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->options=$this->unshiftStack($arguments,array());
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->remove($obj->criteria, $this->options);
                        };
                    break;
                case "count":
                    $this->criteria=$this->unshiftStack($arguments, array());
                    $this->limit=$this->unshiftStack($arguments,0);
                    $this->skip=$this->unshiftStack($arguments,0);
                    $this->query=function($obj) {
                            $obj->result=$obj->collection->count($obj->criteria, $this->limit, $this->skip);
                        };
                    break;
                case "validate":
                case "toIndexString":
                case "ensureIndex":
                case "getIndexInfo":
                case "deleteIndex":
                case "deleteIndexes":
                case "createDBRef":
                case "getDBRef":
                case "distinct":
                case "drop":
                case "getName":
                case "getReadPreference":              
                case "setReadPreference":
                case "group":               
                default:
                    break;
            }
        }
    } 

    private function unshiftStack(&$arguments, $valueIfEmpty=null) {
        if(!empty($arguments)){            
            $value=array_shift($arguments);
            return $value;
        }
        return $valueIfEmpty;
    }

    private function resetGlobalQuery(){
        $this->callback=null;
        $this->objects=null;
        $this->object=null;
        $this->criteria=null;
        $this->result=null;
        $this->exception=null;
        $this->options=array();
    }

}



?>