<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\Plugin\ListFiles;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage;
use Symfony\Component\Filesystem\Filesystem;
use Twistor\FlysystemStreamWrapper;

class FlysystemStorageTest extends ChunkStorageTest
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
        $parentDir = sprintf('/tmp/%s', uniqid('', true));

        $system = new Filesystem();
        $system->mkdir($parentDir);

        $this->parentDir = $parentDir;

        $adapter = new Adapter($this->parentDir);

        $filesystem = new LeagueFilesystem($adapter);
        $filesystem->addPlugin(new ListFiles());

        FlysystemStreamWrapper::register('tests', $filesystem);

        $this->storage = new FlysystemStorage($filesystem, 100000, 'tests:/', $this->chunkKey);
        $this->tmpDir = $this->parentDir . '/' . $this->chunkKey;

        $system->mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        $system = new Filesystem();
        $system->remove($this->parentDir);

        FlysystemStreamWrapper::unregister('tests');
    }
}
