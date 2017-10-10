<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\Plugin\ListFiles;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage;
use Symfony\Component\Filesystem\Filesystem;
use Twistor\FlysystemStreamWrapper;

class FlysystemStorageTest extends ChunkStorageTest
{
    protected $parentDir;
    protected $chunkKey = 'chunks';
    protected $chunkDir;

    public function setUp()
    {
        // create a cache dir
        $parentDir = sprintf('/tmp/%s', uniqid('', true));

        $system = new Filesystem();
        $system->mkdir($parentDir);

        $this->parentDir = $parentDir;

        $adapter = new Adapter($this->parentDir);

        $filesystem = new LeagueFilesystem($adapter);
        $filesystem->addPlugin(new ListFiles());

        FlysystemStreamWrapper::register('tests', $filesystem);

        $this->storage = new FlysystemStorage($filesystem, 100000, 'tests:/', $this->chunkKey);
        $this->tmpDir = $this->parentDir.'/'.$this->chunkKey;

        $system->mkdir($this->tmpDir);
    }

    public function tearDown()
    {
        $system = new Filesystem();
        $system->remove($this->parentDir);

        FlysystemStreamWrapper::unregister('tests');
    }
}
