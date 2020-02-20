<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\PostChunkUploadEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractChunkedUploadTest extends AbstractUploadTest
{
    /**
     * @var int
     */
    protected $total = 6;

    public function testChunkedUpload(): void
    {
        // assemble a request
        $me = $this;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $basename = '';
        $validationCount = 0;

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->client->getContainer()->get('event_dispatcher');

        $dispatcher->addListener(ValidationEvent::NAME, static function (ValidationEvent $event) use (&$validationCount): void {
            ++$validationCount;
        });

        for ($i = 0; $i < $this->total; ++$i) {
            $file = $this->getNextFile($i);

            if ('' === $basename) {
                $basename = $file->getClientOriginalName();
            }

            $dispatcher->addListener(PreUploadEvent::NAME, static function (PreUploadEvent $event) use (&$me, $basename): void {
                $file = $event->getFile();
                $size = $file->getSize();

                $me->assertNotNull($size);
                $me->assertGreaterThan(0, $size);

                $me->assertEquals($file->getBasename(), $basename);
            });

            $this->client->request('POST', $endpoint, $this->getNextRequestParameters($i), [$file], $this->requestHeaders);
            $response = $this->client->getResponse();

            $this->assertTrue($response->isSuccessful());
            $this->assertSame($response->headers->get('Content-Type'), 'application/json');
        }

        $this->assertSame(1, $validationCount);

        foreach ($this->getUploadedFiles() as $file) {
            $this->assertTrue($file->isFile());
            $this->assertTrue($file->isReadable());
            $this->assertSame(120, $file->getSize());
        }
    }

    public function testEvents(): void
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        // prepare listener data
        $me = $this;
        $chunkCount = 0;
        $uploadCount = 0;
        $chunkSize = $this->getNextFile(0)->getSize();

        $dispatcher = $this->client->getContainer()->get('event_dispatcher');

        $dispatcher->addListener(PostChunkUploadEvent::NAME, static function (PostChunkUploadEvent $event) use (&$chunkCount, $chunkSize, &$me): void {
            ++$chunkCount;
            $chunk = $event->getChunk();

            $me->assertEquals($chunkSize, $chunk->getSize());
        });

        $dispatcher->addListener(PostUploadEvent::NAME, static function (Event $event) use (&$uploadCount): void {
            ++$uploadCount;
        });

        for ($i = 0; $i < $this->total; ++$i) {
            $this->client->request('POST', $endpoint, $this->getNextRequestParameters($i), [$this->getNextFile($i)], $this->requestHeaders);
        }

        $this->assertSame($this->total, $chunkCount);
        $this->assertSame(1, $uploadCount);
    }

    abstract protected function getNextRequestParameters(int $i): array;

    abstract protected function getNextFile(int $i): UploadedFile;
}
