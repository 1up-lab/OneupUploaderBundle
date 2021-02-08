<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Gaufrette\Adapter\Local as Adapter;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\GaufretteStorage;
use Symfony\Component\Filesystem\Filesystem;

class GaufretteStorageTest extends ChunkStorageTest
{
    /**
     * @var string
     */
    protected $parentDir;

    /**
     * @var string
     */
    protected $chunkKey = 'chunks';

    /**
     * @var string
     */
    protected $chunkDir;

    protected function setUp(): void
    {
        // create a cache dir
        $parentDir = sprintf('/tmp/%s', uniqid());

        $system = new Filesystem();
        $system->mkdir($parentDir);

        $this->parentDir = $parentDir;

        $adapter = new Adapter($this->parentDir, true);

        $filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($filesystem, 100000, null, $this->chunkKey);
        $this->tmpDir = $this->parentDir . '/' . $this->chunkKey;

        $system->mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        $system = new Filesystem();
        $system->remove($this->parentDir);
    }
}
