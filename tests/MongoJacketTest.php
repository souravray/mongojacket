<?php

class MongoJacketTest extends PHPUnit_Framework_TestCase
{
     /************************************************
     * CONNECTION AND ACCESS
     ************************************************/

    /**
     * Test connection returns Object of MongoJacket\Jacket class
     */
    public function testConnection()
    {
    	$jacket =  new MongoJacket\Jacket();
        $this->assertTrue(is_a($jacket, 'MongoJacket\Jacket'));
    }

    /**
     * Test  DB method of returns Oject of  MongoJacket\DB class
     */
    public function testDBSelection()
    {
        $jacket =  new MongoJacket\Jacket();
        $db = $jacket->db("TestingDB");
        $this->assertTrue(is_a($db, 'MongoJacket\DB'));
    }

    /**
     * Test  Collection method of returns Oject of  MongoJacket\Collection class
     */
    public function testCollectionSelection()
    {
        $jacket =  new MongoJacket\Jacket();
        $collection = $jacket->db("TestingDB")->collection('MyCollection');
        $this->assertTrue(is_a($collection, 'MongoJacket\Collection'));
    }

     /**
     * Test DB method of using a DB pool
     */
    public function testDBPool()
    {
        $jacket1 =  new MongoJacket\Jacket();
        $db1=$jacket1->db("TestingDB");
        $db2=$jacket1->db("TestingDB");

        $jacket2=  new MongoJacket\Jacket();
        $db3=$jacket2->db("TestingDB");
        $db4=$jacket2->db("TestingDB");

        $this->assertTrue($db1===$db2);
        $this->assertTrue($db4===$db3);
        $this->assertFalse($db1===$db4);
    }

    /**
     * Test Collection method of using a Collection pool
     */
    public function testCollectionPool()
    {
        $jacket1 =  new MongoJacket\Jacket();
        $col1=$jacket1->db("TestingDB")->collection('MyCollection');
        $col2=$jacket1->db("TestingDB")->collection('MyCollection');

        $jacket2=  new MongoJacket\Jacket();
        $col3=$jacket2->db("TestingDB")->collection('MyCollection');
        $col4=$jacket2->db("TestingDB")->collection('MyCollection');

        $this->assertTrue($col1===$col2);
        $this->assertTrue($col4===$col3);
        $this->assertFalse($col1===$col4);
    }    
 }
?>