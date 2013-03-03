<?php
class CallbackTest extends PHPUnit_Framework_TestCase
{
    protected $unquieTestKey;
    protected $collection;

    public function setUp() {
        $this->unquieTestKey=uniqid();
        $jacket =  new MongoJacket\Jacket();
        $this->collection = $jacket->db("TestingDB")->collection('MyCollection');
    }

    /************************************************
     * Test Document Insert with Callback
     ************************************************/
    public function testDocumentInsert() {

        $returnValue=$this->collection->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "testing"),
                        function ($result, $error) {
                           return "yes";
                        }
                    );
        $this->assertSame("yes",$returnValue);
    }

    /************************************************
     * Test Document Insert with Callback  
     ************************************************/
    public function testDocumentFind() {

        $returnValue=$this->collection->Find(array("purpose"=> "testing"),
                                            function($results, $error) {
                                                return $results->count();
                                            });

        $this->assertTrue(is_int($returnValue) && $returnValue>0);
    }

    /************************************************
     * Test Document nested Callback
     ************************************************/
    public function testNestedCallbacks() {
        $this->collection->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "testing"));

        $returnValue=$this->collection->FindOne(array(
                                        "name"=> "test-insert-".$this->unquieTestKey),
                                        function($result, $error) {
                                            return $this->Save($result,
                                                function($result, $error){
                                                    return "Saved";
                                                }
                                            );
                                        });

         $this->assertSame("Saved",$returnValue);
    }
}
?>