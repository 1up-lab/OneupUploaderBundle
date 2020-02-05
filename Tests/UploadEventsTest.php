<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests;

use Oneup\UploaderBundle\UploadEvents;
use PHPUnit\Framework\TestCase;

class UploadEventsTest extends TestCase
{
    public function testPreUploadCanBePassedAMapping(): void
    {
        $event = UploadEvents::preUpload('gallery');

        $this->assertSame(UploadEvents::PRE_UPLOAD . '.gallery', $event);
    }

    public function testPostUploadCanBePassedAMapping(): void
    {
        $event = UploadEvents::postUpload('gallery');

        $this->assertSame(UploadEvents::POST_UPLOAD . '.gallery', $event);
    }

    public function testPostPersistCanBePassedAMapping(): void
    {
        $event = UploadEvents::postPersist('gallery');

        $this->assertSame(UploadEvents::POST_PERSIST . '.gallery', $event);
    }

    public function testPostChunkUploadCanBePassedAMapping(): void
    {
        $event = UploadEvents::postChunkUpload('gallery');

        $this->assertSame(UploadEvents::POST_CHUNK_UPLOAD . '.gallery', $event);
    }

    public function testValidationCanBePassedAMapping(): void
    {
        $event = UploadEvents::validation('gallery');

        $this->assertSame(UploadEvents::VALIDATION . '.gallery', $event);
    }
}
