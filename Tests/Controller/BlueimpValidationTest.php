<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\UploadEvents;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlueimpValidationTest extends AbstractValidationTest
{
    public function testAgainstMaxSize()
    {
        // assemble a request
        /** @var Client $client */
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getOversizedFile(), $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
        $this->assertFalse(strpos($response->getContent(), 'error.maxsize'), 'Failed to translate error id into lang');
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

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithCorrectMimeType(), $this->requestHeaders);

        $this->assertSame(1, $validationCount);
    }

    public function testAgainstCorrectMimeType()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithCorrectMimeType(), $this->requestHeaders);
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

    public function testAgainstIncorrectMimeType()
    {
        $this->markTestSkipped('Mock mime type getter.');

        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithIncorrectMimeType(), $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    protected function getConfigKey()
    {
        return 'blueimp_validation';
    }

    protected function getRequestParameters()
    {
        return [];
    }

    protected function getOversizedFile()
    {
        return ['files' => [new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain',
            512
        )]];
    }

    protected function getFileWithCorrectMimeType()
    {
        return ['files' => [new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain',
            128
        )]];
    }

    protected function getFileWithCorrectMimeTypeAndIncorrectExtension()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txxt',
            'text/plain',
            128
        );
    }

    protected function getFileWithIncorrectMimeType()
    {
        return [new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif',
            128
        )];
    }
}
