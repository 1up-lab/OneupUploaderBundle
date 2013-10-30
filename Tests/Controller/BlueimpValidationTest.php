<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractValidationTest;
use Oneup\UploaderBundle\UploadEvents;

class BlueimpValidationTest extends AbstractValidationTest
{
    public function testAgainstMaxSize()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getOversizedFile(), $this->requestHeaders);
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

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithCorrectMimeType(), $this->requestHeaders);

        $this->assertEquals(1, $validationCount);
    }

    public function testAgainstCorrectMimeType()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithCorrectMimeType(), $this->requestHeaders);
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

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getFileWithIncorrectMimeType(), $this->requestHeaders);
        $response = $client->getResponse();

        //$this->assertTrue($response->isNotSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertCount(0, $this->getUploadedFiles());
    }

    protected function getConfigKey()
    {
        return 'blueimp_validation';
    }

    protected function getRequestParameters()
    {
        return array();
    }

    protected function getOversizedFile()
    {
        return array('files' => array(new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain',
            512
        )));
    }

    protected function getFileWithCorrectMimeType()
    {
        return array('files' => array(new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/jpg',
            128
        )));
    }

    protected function getFileWithIncorrectMimeType()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif',
            128
        ));
    }
}
