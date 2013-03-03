<?php
class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $unquieTestKey;
    protected $collection;

    public function setUp() {
        $this->unquieTestKey=uniqid();
        $jacket =  new MongoJacket\Jacket();
        $this->collection = $jacket->db("TestingDB")->collection('MyCollection');
    }

    /************************************************
     * Test Document Insert with Validator
     ************************************************/
    public function testDocumentInsert() {
        $this->collection->validator("purpose", function($var) {
                                    return $var!="production";
                                });
        
        $returnValueFail=$this->collection->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "production") );

        $this->assertTrue(is_a($returnValueFail,'MongoJacket\Exception'));

        $returnValueSuccess=$this->collection->Insert(array(
                            "name"=> "test-insert-".$this->unquieTestKey , 
                            "purpose"=> "testing") );
        $this->assertTrue($returnValueSuccess===TRUE);
    }
}
?>