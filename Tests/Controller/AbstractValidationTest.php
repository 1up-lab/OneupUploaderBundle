<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\UploadEvents;

abstract class AbstractValidationTest extends AbstractControllerTest
{
    abstract protected function getFileWithCorrectMimeType();
    abstract protected function getFileWithIncorrectMimeType();
    abstract protected function getOversizedFile();

    public function testAgainstMaxSize()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getOversizedFile()), $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    public function testEvents()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;

        $dispatcher->addListener(UploadEvents::VALIDATION, function() use (&$validationCount) {
            ++ $validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithCorrectMimeType()), $this->requestHeaders);

        $this->assertEquals(1, $validationCount);
    }

    public function testIfRequestIsAvailableInEvent()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;
        $me = $this;

        $dispatcher->addListener(UploadEvents::VALIDATION, function(ValidationEvent $event) use (&$validationCount, &$me) {
            $me->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $event->getRequest());

            // to be sure this listener is called
            ++ $validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithCorrectMimeType()), $this->requestHeaders);

        $this->assertEquals(1, $validationCount);
    }

    public function testAgainstCorrectMimeType()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithCorrectMimeType()), $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(1, $this->getUploadedFiles());

        foreach ($this->getUploadedFiles() as $file) {
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

        $client->request('POST', $endpoint, $this->getRequestParameters(), array($this->getFileWithIncorrectMimeType()), $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }
}
