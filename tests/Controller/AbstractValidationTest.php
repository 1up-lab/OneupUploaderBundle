<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\ValidationEvent;

abstract class AbstractValidationTest extends AbstractControllerTest
{
    public function testAgainstMaxSize(): void
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

    public function testEvents(): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;

        $dispatcher->addListener(ValidationEvent::NAME, static function () use (&$validationCount): void {
            ++$validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeType()], $this->requestHeaders);

        $this->assertSame(1, $validationCount);
    }

    public function testIfRequestIsAvailableInEvent(): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $validationCount = 0;
        $me = $this;

        $dispatcher->addListener(ValidationEvent::NAME, static function (ValidationEvent $event) use (&$validationCount, &$me): void {
            $me->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $event->getRequest());

            // to be sure this listener is called
            ++$validationCount;
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeType()], $this->requestHeaders);

        $this->assertSame(1, $validationCount);
    }

    public function testAgainstCorrectMimeType(): void
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

    public function testAgainstCorrectMimeTypeAndIncorrectExtension(): void
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), [$this->getFileWithCorrectMimeTypeAndIncorrectExtension()], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    public function testAgainstIncorrectMimeType(): void
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

    /**
     * @return mixed
     */
    abstract protected function getFileWithCorrectMimeType();

    /**
     * @return mixed
     */
    abstract protected function getFileWithCorrectMimeTypeAndIncorrectExtension();

    /**
     * @return mixed
     */
    abstract protected function getFileWithIncorrectMimeType();

    /**
     * @return mixed
     */
    abstract protected function getOversizedFile();

    abstract protected function getRequestParameters(): array;
}
