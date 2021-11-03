<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter as Adapter;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @throws FilesystemException
     */
    public function testGetChunks(): void
    {
        $uuid = uniqid('', true);
        $dir = $this->tmpDir . '/' . $uuid;
        $system = new Filesystem();
        $system->mkdir($dir);
        $timeFrom = time();
        $system->mkdir($dir . '/shouldNotBeListed');
        $system->dumpFile($dir . '/chunk1', 'test');
        $system->dumpFile($dir . '/chunk2', 'test');
        $system->dumpFile($dir . '/chunk3', 'test');
        $timeTo = time();

        $files = $this->storage->getChunks($uuid);
        $this->assertCount(3, $files);
        $file = $files[0];
        $this->assertSame($this->chunkKey . '/' . $uuid . '/chunk1', $file['path']);
        $this->assertSame('file', $file['type']);
        $this->assertGreaterThanOrEqual($timeFrom, $file['timestamp']);
        $this->assertLessThanOrEqual($timeTo, $file['timestamp']);
        $this->assertSame(4, $file['size']);
    }
}
