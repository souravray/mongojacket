<?php

class MongoJacketTest extends PHPUnit_Framework_TestCase
{
    protected $unquieTestKey;
    public function setUp() {
        $this->unquieTestKey=uniqid();
    }

    /************************************************
     * Autoloading
     ************************************************/
    public function testAutoloadConstants() {
        $jacket =  new MongoJacket\Jacket();
        $collection = $jacket->db("TestingDB")->collection('MyCollection');
        $this->assertTrue(JK_EVNT_INIT=='init');
    }

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

        $this->assertSame($db1,$db2);
        $this->assertSame($db4,$db3);
        $this->assertNotSame($db1,$db4);
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

        $this->assertSame($col1,$col2);
        $this->assertSame($col4,$col3);
        $this->assertNotSame($col1,$col4);
    }

    /************************************************
     * CONNECTION AND ACCESS
     ************************************************/

    /**
     * Test Insert method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentInsert()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $isSucess=$col->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "testing")
                    );
        $this->assertTrue($isSucess===TRUE);
    }

    /**
     * Test Find method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentFind()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $results=$col->Find(array("purpose"=> "testing"));
        $this->assertFalse(is_a($results,'MongoJacket\Exception'));
        $this->assertTrue($results->count()>0);
        for($i=0; $i<$results->count();$i++){
            $result=$results->getNext();
            $this->assertFalse(is_null($result));
        }
    }

    /**
     * Test FindOne method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentFindOne()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $isSucess=$col->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "testing")
                    );
        $result=$col->FindOne(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertFalse(is_a($result,'MongoJacket\Exception'));
        $this->assertFalse(is_null($result));
        $this->assertTrue(is_array($result));
    }

    /**
     * Test Save method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentSave()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $col->Insert(array(
                        "name"=> "test-insert-".$this->unquieTestKey , 
                        "purpose"=> "testing")
                    );
        
        $resultToUpdate=$col->FindOne(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertSame("testing",$resultToUpdate["purpose"]);
        $resultToUpdate["purpose"]="tested";
        $col->Save($resultToUpdate);
        
        $results=$col->Find(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertTrue($results->count()==1);  
        $result=$results->getNext();
        $this->assertFalse(is_null($result));
        $this->assertTrue(isset($result["purpose"]));
        $this->assertSame("tested",$result["purpose"]);
    }

    /**
     * Test Update method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentUpdate()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $col->Insert(array(
                        "name"=> "test-insert-".$this->unquieTestKey , 
                        "purpose"=> "testing")
                    );
        
        $resultToUpdate=$col->FindOne(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertSame("testing",$resultToUpdate["purpose"]);

        $col->Update( array("name"=> "test-insert-".$this->unquieTestKey),
                        array(
                        "name"=> "test-insert-".$this->unquieTestKey , 
                        "purpose"=> "updated"));
        
        $results=$col->Find(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertTrue($results->count()==1);  
        $result=$results->getNext();
        $this->assertFalse(is_null($result));
        $this->assertTrue(isset($result["purpose"]));
        $this->assertSame("updated",$result["purpose"]);
    }

    /**
     * Test Batch insert method of returns Oject of  MongoJacket\DB class
     */
    public function testDocumentBatchInsert()
    {
        $jacket=new MongoJacket\Jacket();
        $col=$jacket->db("TestingDB")->collection('MyCollection');
        $col->BatchInsert(array(
                        array( "name"=> "test-insert-".$this->unquieTestKey , 
                                "purpose"=> "testing") ,
                        array( "name"=> "test-insert-".$this->unquieTestKey , 
                                "purpose"=> "testing") ,
                        array( "name"=> "test-insert-".$this->unquieTestKey , 
                                "purpose"=> "testing")
                        )
                    );
        
        $results=$col->Find(array("name"=> "test-insert-".$this->unquieTestKey));
        $this->assertTrue($results->count()==3);  
    }
}
?>