<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemStorageTest extends ChunkStorageTest
{
    /**
     * @var string
     */
    protected $tmpDir;

    protected function setUp(): void
    {
        // create a cache dir
        $tmpDir = sprintf('/tmp/%s', uniqid());

        $system = new Filesystem();
        $system->mkdir($tmpDir);

        $this->tmpDir = $tmpDir;
        $this->storage = new FilesystemStorage($this->tmpDir);
    }

    protected function tearDown(): void
    {
        $system = new Filesystem();
        $system->remove($this->tmpDir);
    }
}
