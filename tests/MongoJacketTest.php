<?php

class MongoJacketTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /************************************************
     * CONNECTION
     ************************************************/

    /**
     * Test version constant is string
     */
    public function testConnection()
    {
    	$jacket =  new MongoJacket\Jacket();
        $this->assertTrue(is_a($jacket, 'MongoJacket\Jacket'));
    }


 }
?>