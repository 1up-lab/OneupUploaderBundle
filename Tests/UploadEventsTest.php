<?php

namespace Oneup\UploaderBundle\Tests;

use Oneup\UploaderBundle\UploadEvents;
use PHPUnit\Framework\TestCase;

class UploadEventsTest extends TestCase
{
    public function testPreUploadCanBePassedAMapping()
    {
        $event = UploadEvents::preUpload('gallery');

        $this->assertEquals(UploadEvents::PRE_UPLOAD . '.gallery', $event);
    }

    public function testPostUploadCanBePassedAMapping()
    {
        $event = UploadEvents::postUpload('gallery');

        $this->assertEquals(UploadEvents::POST_UPLOAD . '.gallery', $event);
    }

    public function testPostPersistCanBePassedAMapping()
    {
        $event = UploadEvents::postPersist('gallery');

        $this->assertEquals(UploadEvents::POST_PERSIST . '.gallery', $event);
    }

    public function testPostChunkUploadCanBePassedAMapping()
    {
        $event = UploadEvents::postChunkUpload('gallery');

        $this->assertEquals(UploadEvents::POST_CHUNK_UPLOAD . '.gallery', $event);
    }

    public function testValidationCanBePassedAMapping()
    {
        $event = UploadEvents::validation('gallery');

        $this->assertEquals(UploadEvents::VALIDATION . '.gallery', $event);
    }
}
