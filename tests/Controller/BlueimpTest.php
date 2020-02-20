<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlueimpTest extends AbstractUploadTest
{
    public function testSingleUpload(): void
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getRequestFile(), $this->requestHeaders);
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

    public function testResponseForOldBrowsers(): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getRequestFile());
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'text/plain; charset=UTF-8');
        $this->assertCount(1, $this->getUploadedFiles());
    }

    public function testEvents(): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $dispatcher = $client->getContainer()->get('event_dispatcher');

        // event data
        $me = $this;
        $uploadCount = 0;
        $preValidation = 1;

        $dispatcher->addListener(PreUploadEvent::NAME, static function (PreUploadEvent $event) use (&$me, &$preValidation): void {
            $preValidation -= 2;

            $file = $event->getFile();
            $request = $event->getRequest();

            // add a new key to the attribute list
            $request->attributes->set('grumpy', 'cat');

            $me->assertInstanceOf(UploadedFile::class, $file);
        });

        $dispatcher->addListener(PostUploadEvent::NAME, static function (PostUploadEvent $event) use (&$uploadCount, &$me, &$preValidation): void {
            ++$uploadCount;
            $preValidation *= -1;

            $file = $event->getFile();
            $request = $event->getRequest();

            $me->assertInstanceOf(File::class, $file);
            $me->assertEquals(128, $file->getSize());
            $me->assertEquals('cat', $request->get('grumpy'));
        });

        $client->request('POST', $endpoint, $this->getRequestParameters(), $this->getRequestFile(), $this->requestHeaders);

        $this->assertCount(1, $this->getUploadedFiles());

        $this->assertCount($uploadCount, $this->getUploadedFiles());
        $this->assertSame(1, $preValidation);
    }

    protected function getConfigKey(): string
    {
        return 'blueimp';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getRequestFile(): array
    {
        return ['files' => [
            new UploadedFile($this->createTempFile(128), 'cat.txt', 'text/plain', null, true),
        ]];
    }
}
