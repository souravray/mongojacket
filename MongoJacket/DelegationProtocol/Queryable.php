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
    public $object=null;
    public $criteria=null;

    final protected function query($method=null, $arguments){
        switch ($method){
            case "aggregate":
            case "batchInsert":
            case "count":
            case "createDBRef":
            case "deleteIndex":
            case "deleteIndexes":
            case "distinct":
            case "drop":
            case "ensureIndex":
            case "find":
            case "findAndModify":
            case "findOne":
            case "getDBRef":
            case "getIndexInfo":
            case "getName":
            case "getReadPreference":
            case "group":
            case "insert":
            case "remove":
            case "save":
            case "setReadPreference":
            case "toIndexString":
            case "update":
            case "validate":
            default:
                break;
        }
    } 

}

?>