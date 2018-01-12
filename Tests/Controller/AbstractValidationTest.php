<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\UploadEvents;

abstract class AbstractValidationTest extends AbstractControllerTest
{
    public function testAgainstMaxSize()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getOversizedFile()], $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    public function testEvents()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;

        $dispatcher->addListener(UploadEvents::VALIDATION, function () use (&$validationCount) {
            ++$validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeType()], $this->requestHeaders);

        $this->assertSame(1, $validationCount);
    }

    public function testIfRequestIsAvailableInEvent()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;
        $me = $this;

        $dispatcher->addListener(UploadEvents::VALIDATION, function (ValidationEvent $event) use (&$validationCount, &$me) {
            $me->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $event->getRequest());

            // to be sure this listener is called
            ++$validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeType()], $this->requestHeaders);

        $this->assertSame(1, $validationCount);
    }

    public function testAgainstCorrectMimeType()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeType()], $this->requestHeaders);
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

    public function testAgainstCorrectMimeTypeAndIncorrectExtension()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeTypeAndIncorrectExtension()], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    public function testAgainstIncorrectMimeType()
    {
        $this->markTestSkipped('Mock mime type getter.');

        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithIncorrectMimeType()], $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    abstract protected function getFileWithCorrectMimeType();

    abstract protected function getFileWithCorrectMimeTypeAndIncorrectExtension();

    abstract protected function getFileWithIncorrectMimeType();

    abstract protected function getOversizedFile();
}
