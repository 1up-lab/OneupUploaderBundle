<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\UploaderResponse;

class TestUploaderResponse extends \PHPUnit_Framework_TestCase
{
    public function testCreationOfResponse()
    {
        $response = new UploaderResponse();
        
        $this->assertTrue($response->getSuccess());
        $this->assertNull($response->getError());
    }
    
    public function testFillOfResponse()
    {
        $response = new UploaderResponse();
        
        $cat = 'is grumpy';
        $dog = 'has no idea';
        
        $response['cat'] = $cat;
        $response['dog'] = $dog;
        $response->setSuccess(false);
        
        $assembled = $response->assemble();
        
        $this->assertTrue(is_array($assembled));
        $this->assertArrayHasKey('cat', $assembled);
        $this->assertArrayHasKey('dog', $assembled);
        $this->assertEquals($assembled['cat'], $cat);
        $this->assertEquals($assembled['dog'], $dog);
        $this->assertFalse($response->getSuccess());
        $this->assertNull($response->getError());
    }
    
    public function testError()
    {
        $response = new UploaderResponse();
        $response->setError('This response is grumpy');
        
        $this->assertEquals($response->getError(), 'This response is grumpy');
    }
    
    public function testOverwriteOfInternals()
    {
        $response = new UploaderResponse();
        $response['success'] = false;
        $response['error'] = 42;
        
        $this->assertTrue($response->getSuccess());
        $this->assertNull($response->getError());
        
        $assembled = $response->assemble();
        
        $this->assertTrue($assembled['success']);
        $this->assertArrayNotHasKey('error', $assembled);
    }
}