<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Oneup\UploaderBundle\UploadEvents;

abstract class AbstractUploadTest extends AbstractControllerTest
{
    public function setUp()
    {
        parent::setUp();

        $this->createdFiles = [];
    }

    public function testSingleUpload()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getRequestFile()], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(1, $this->getUploadedFiles());

        foreach ($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertSame(128, $file->getSize());
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
        $preValidation = 1;

        $dispatcher->addListener(UploadEvents::PRE_UPLOAD, function (PreUploadEvent $event) use (&$uploadCount, &$me, &$preValidation) {
            $preValidation -= 2;

            $file = $event->getFile();
            $request = $event->getRequest();

            // add a new key to the attribute list
            $request->attributes->set('grumpy', 'cat');

            $me->assertInstanceOf('Symfony\Component\HttpFoundation\File\UploadedFile', $file);
        });

        $dispatcher->addListener(UploadEvents::POST_UPLOAD, function (PostUploadEvent $event) use (&$uploadCount, &$me, &$preValidation) {
            ++$uploadCount;
            $preValidation *= -1;

            $file = $event->getFile();
            $request = $event->getRequest();

            $me->assertInstanceOf('Symfony\Component\HttpFoundation\File\File', $file);
            $me->assertEquals(128, $file->getSize());
            $me->assertEquals('cat', $request->get('grumpy'));
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getRequestFile()]);

        $this->assertCount(1, $this->getUploadedFiles());
        $this->assertSame($uploadCount, count($this->getUploadedFiles()));
        $this->assertSame(1, $preValidation);
    }

    abstract protected function getRequestParameters();

    abstract protected function getRequestFile();
}
