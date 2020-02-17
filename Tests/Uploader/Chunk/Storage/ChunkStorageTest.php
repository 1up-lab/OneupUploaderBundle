<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class ChunkStorageTest extends TestCase
{
    /**
     * @var string
     */
    protected $tmpDir;

    /**
     * @var ChunkStorageInterface
     */
    protected $storage;

    public function testExistanceOfTmpDir(): void
    {
        $this->assertDirectoryExists($this->tmpDir);
        $this->assertIsWritable($this->tmpDir);
    }

    public function testFillOfTmpDir(): void
    {
        $finder = new Finder();
        $finder->in($this->tmpDir);

        $numberOfFiles = 10;

        $this->fillDirectory($numberOfFiles);
        $this->assertCount($numberOfFiles, $finder);
    }

    public function testChunkCleanup(): void
    {
        // get a manager configured with a max-age of 5 minutes
        $maxage = 5 * 60;
        $numberOfFiles = 10;

        $finder = new Finder();
        $finder->in($this->tmpDir);

        $this->fillDirectory($numberOfFiles);
        $this->assertCount($numberOfFiles, $finder);

        $this->storage->clear($maxage);

        $this->assertDirectoryExists($this->tmpDir);
        $this->assertIsWritable($this->tmpDir);

        $this->assertCount(5, $finder);

        foreach ($finder as $file) {
            /* @var \SplFileInfo $file */
            $this->assertGreaterThanOrEqual(time() - $maxage, $file->getMTime());
        }
    }

    public function testClearIfDirectoryDoesNotExist(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tmpDir);

        $this->storage->clear(10);

        // yey, no exception
        $this->assertTrue(true);
    }

    protected function fillDirectory(int $number): void
    {
        $system = new Filesystem();

        for ($i = 0; $i < $number; ++$i) {
            $system->touch(sprintf('%s/%s', $this->tmpDir, uniqid('', true)), time() - $i * 60);
        }
    }
}
