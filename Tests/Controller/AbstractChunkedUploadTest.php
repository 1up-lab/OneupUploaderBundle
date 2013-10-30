<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\EventDispatcher\Event;
use Oneup\UploaderBundle\Tests\Controller\AbstractUploadTest;
use Oneup\UploaderBundle\Event\PostChunkUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\UploadEvents;

abstract class AbstractChunkedUploadTest extends AbstractUploadTest
{
    protected $total = 6;

    abstract protected function getNextRequestParameters($i);
    abstract protected function getNextFile($i);

    public function testChunkedUpload()
    {
        // assemble a request
        $me = $this;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $basename = '';
        $validationCount = 0;

        for ($i = 0; $i < $this->total; $i ++) {
            $file = $this->getNextFile($i);

            if ($basename === '') {
                $basename = $file->getClientOriginalName();
            }

            $client = static::createClient();
            $dispatcher = $client->getContainer()->get('event_dispatcher');

            $dispatcher->addListener(UploadEvents::PRE_UPLOAD, function(PreUploadEvent $event) use (&$me, $basename) {
                $file = $event->getFile();

                $me->assertNotNull($file->getClientSize());
                $me->assertGreaterThan(0, $file->getClientSize());
                $me->assertEquals($file->getBasename(), $basename);
            });

            $dispatcher->addListener(UploadEvents::VALIDATION, function(ValidationEvent $event) use (&$validationCount) {
                ++ $validationCount;
            });

            $client->request('POST', $endpoint, $this->getNextRequestParameters($i), array($file), $this->requestHeaders);
            $response = $client->getResponse();

            $this->assertTrue($response->isSuccessful());
            $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        }

        $this->assertEquals(1, $validationCount);

        foreach ($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertEquals(120, $file->getSize());
        }
    }

    public function testEvents()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        // prepare listener data
        $me = $this;
        $chunkCount = 0;
        $uploadCount = 0;
        $chunkSize = $this->getNextFile(0)->getSize();

        for ($i = 0; $i < $this->total; $i ++) {
            // each time create a new client otherwise the events won't get dispatched
            $client = static::createClient();
            $dispatcher = $client->getContainer()->get('event_dispatcher');

            $dispatcher->addListener(UploadEvents::POST_CHUNK_UPLOAD, function(PostChunkUploadEvent $event) use (&$chunkCount, $chunkSize, &$me) {
                ++ $chunkCount;

                $chunk = $event->getChunk();

                $me->assertEquals($chunkSize, $chunk->getSize());
            });

            $dispatcher->addListener(UploadEvents::POST_UPLOAD, function(Event $event) use (&$uploadCount) {
                ++ $uploadCount;
            });

            $client->request('POST', $endpoint, $this->getNextRequestParameters($i), array($this->getNextFile($i)), $this->requestHeaders);
        }

        $this->assertEquals($this->total, $chunkCount);
        $this->assertEquals(1, $uploadCount);
    }
}
