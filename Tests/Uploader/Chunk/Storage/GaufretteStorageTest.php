<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\GaufretteStorage;
use Symfony\Component\Filesystem\Filesystem;
use Gaufrette\Adapter\Local as Adapter;
use Gaufrette\Filesystem as GaufretteFilesystem;

class GaufretteStorageTest extends ChunkStorageTest
{
    protected $parentDir;
    protected $chunkKey = 'chunks';
    protected $chunkDir;

    public function setUp()
    {
        // create a cache dir
        $parentDir = sprintf('/tmp/%s', uniqid());

        $system = new Filesystem();
        $system->mkdir($parentDir);

        $this->parentDir = $parentDir;

        $adapter = new Adapter($this->parentDir, true);

        $filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($filesystem, 100000, null, $this->chunkKey);
        $this->tmpDir = $this->parentDir.'/'.$this->chunkKey;

        $system->mkdir($this->tmpDir);

    }

    public function tearDown()
    {
        $system = new Filesystem();
        $system->remove($this->parentDir);
    }

}
