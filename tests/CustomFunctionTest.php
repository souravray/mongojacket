<?php
class CustomFunctionTest extends PHPUnit_Framework_TestCase
{
    protected $collection;

    public function setUp() {
        $jacket =  new MongoJacket\Jacket();
        $this->collection = $jacket->db("TestingDB")->collection('MyCollection');
    }

    /************************************************
     * Test Document Register a custome function
     ************************************************/
    public function testRegisterCustomFunction() {

        $this->collection->bind("countTestEntries", function ()  {
            $results=$this->Find(array("purpose"=> "testing"));
            return $results->count();  
        });

        $returnValue = $this->collection->countTestEntries();

        $this->assertTrue(is_int($returnValue));
    }
}
?>