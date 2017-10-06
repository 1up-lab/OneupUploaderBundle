<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class ChunkStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    /**
     * @var ChunkStorageInterface
     */
    protected $storage;

    public function testExistanceOfTmpDir()
    {
        $this->assertTrue(is_dir($this->tmpDir));
        $this->assertTrue(is_writable($this->tmpDir));
    }

    public function testFillOfTmpDir()
    {
        $finder = new Finder();
        $finder->in($this->tmpDir);

        $numberOfFiles = 10;

        $this->fillDirectory($numberOfFiles);
        $this->assertCount($numberOfFiles, $finder);
    }

    public function testChunkCleanup()
    {
        // get a manager configured with a max-age of 5 minutes
        $maxage = 5 * 60;
        $numberOfFiles = 10;

        $finder = new Finder();
        $finder->in($this->tmpDir);

        $this->fillDirectory($numberOfFiles);
        $this->assertCount($numberOfFiles, $finder);

        $this->storage->clear($maxage);

        $this->assertTrue(is_dir($this->tmpDir));
        $this->assertTrue(is_writable($this->tmpDir));

        $this->assertCount(5, $finder);

        foreach ($finder as $file) {
            $this->assertGreaterThanOrEqual(time() - $maxage, filemtime($file));
        }
    }

    public function testClearIfDirectoryDoesNotExist()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tmpDir);

        $this->storage->clear(10);

        // yey, no exception
        $this->assertTrue(true);
    }

    protected function fillDirectory($number)
    {
        $system = new Filesystem();

        for ($i = 0; $i < $number; ++$i) {
            $system->touch(sprintf('%s/%s', $this->tmpDir, uniqid('', true)), time() - $i * 60);
        }
    }
}
