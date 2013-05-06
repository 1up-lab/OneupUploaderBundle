<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

abstract class AbstractChunkedControllerTest extends AbstractControllerTest
{
    protected $total = 6;
    
    abstract protected function getNextRequestParameters($i);
    abstract protected function getNextFile($i);
    
    public function testChunkedUpload()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        for($i = 0; $i < $this->total; $i ++) {
            $client->request('POST', $endpoint, $this->getNextRequestParameters($i), array($this->getNextFile($i)));
            $response = $client->getResponse();
        
            $this->assertTrue($response->isSuccessful());
            $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        }
        
        foreach($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertEquals(120, $file->getSize());
        }
    }
}
