<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;
use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostUploadEvent;

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
    
    public function testEvents()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');
        
        // event data
        $me = $this;
        $uploadCount = 0;
        
        $dispatcher->addListener(UploadEvents::POST_UPLOAD, function(PostUploadEvent $event) use (&$uploadCount, &$me) {
            ++ $uploadCount;
            
            $file = $event->getFile();
            
            $me->assertInstanceOf('Symfony\Component\HttpFoundation\File\File', $file);
            $me->assertEquals(128, $file->getSize());
        });
        
        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getRequestFile()));
        
        $this->assertCount(1, $this->getUploadedFiles());
        $this->assertEquals($uploadCount, count($this->getUploadedFiles()));
    }
}
