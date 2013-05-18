<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

abstract class AbstractUploadTest extends AbstractControllerTest
{
    abstract protected function getRequestParameters();
    abstract protected function getRequestFile();
    
    public function setUp()
    {
        parent::setUp();

        $this->createdFiles = array();
    }
    
    public function testSingleUpload()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getRequestFile()));
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
}
