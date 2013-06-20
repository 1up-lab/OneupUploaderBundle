<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Chunk\ChunkManager;

class ChunkManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;

    public function setUp()
    {
        // create a cache dir
        $tmpDir = sprintf('/tmp/%s', uniqid());

        $system = new Filesystem();
        $system->mkdir($tmpDir);

        $this->tmpDir = $tmpDir;
    }

    public function tearDown()
    {
        $system = new Filesystem();
        $system->remove($this->tmpDir);
    }

    public function testExistanceOfTmpDir()
    {
        $this->assertTrue(is_dir($this->tmpDir));
        $this->assertTrue(is_writeable($this->tmpDir));
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
        $maxage  = 5 * 60;
        $manager = $this->getManager($maxage);
        $numberOfFiles = 10;

        $finder = new Finder();
        $finder->in($this->tmpDir);

        $this->fillDirectory($numberOfFiles);
        $this->assertCount(10, $finder);

        $manager->clear();

        $this->assertTrue(is_dir($this->tmpDir));
        $this->assertTrue(is_writeable($this->tmpDir));

        $this->assertCount(5, $finder);

        foreach ($finder as $file) {
            $this->assertGreaterThanOrEqual(time() - $maxage, filemtime($file));
        }
    }

    public function testClearIfDirectoryDoesNotExist()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tmpDir);

        $manager = $this->getManager(10);
        $manager->clear();

        // yey, no exception
        $this->assertTrue(true);
    }

    protected function getManager($maxage)
    {
        return new ChunkManager(array(
            'directory' => $this->tmpDir,
            'maxage' => $maxage
        ));
    }

    protected function fillDirectory($number)
    {
        $system = new Filesystem();

        for ($i = 0; $i < $number; $i ++) {
            $system->touch(sprintf('%s/%s', $this->tmpDir, uniqid()), time() - $i * 60);
        }
    }
}
