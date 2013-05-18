<?php

namespace Oneup\UploaderBundle\Tests\Controller;

abstract class AbstractValidationTest extends AbstractControllerTest
{
    abstract protected function getFileWithCorrectExtension();
    abstract protected function getFileWithIncorrectExtension();
    abstract protected function getFileWithCorrectMimeType();
    abstract protected function getFileWithIncorrectMimeType();
    
    public function testAgainstCorrectExtension()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithCorrectExtension()));
        $response = $client->getResponse();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(1, $this->getUploadedFiles());
        
        foreach($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertEquals(128, $file->getSize());
        }
    }
    
    public function testAgainstIncorrectExtension()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithIncorrectExtension()));
        $response = $client->getResponse();
        
        //$this->assertTrue($response->isNotSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }
    
    public function testAgainstCorrectMimeType()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithCorrectMimeType()));
        $response = $client->getResponse();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(1, $this->getUploadedFiles());
        
        foreach($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertEquals(128, $file->getSize());
        }
    }
    
    public function testAgainstIncorrectMimeType()
    {
        $this->markTestSkipped('Mock mime type getter.');
        
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithIncorrectMimeType()));
        $response = $client->getResponse();
        
        //$this->assertTrue($response->isNotSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }
}
