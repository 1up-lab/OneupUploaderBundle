<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemStorageTest extends ChunkStorageTest
{
    protected $tmpDir;

    public function setUp()
    {
        // create a cache dir
        $tmpDir = sprintf('/tmp/%s', uniqid());

        $system = new Filesystem();
        $system->mkdir($tmpDir);

        $this->tmpDir = $tmpDir;
        $this->storage = new FilesystemStorage([
            'directory' => $this->tmpDir,
        ]);
    }

    public function tearDown()
    {
        $system = new Filesystem();
        $system->remove($this->tmpDir);
    }
}
