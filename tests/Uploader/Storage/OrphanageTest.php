<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class OrphanageTest extends TestCase
{
    /**
     * @var string
     */
    protected $tempDirectory;

    /**
     * @var string
     */
    protected $realDirectory;

    /**
     * @var OrphanageStorageInterface
     */
    protected $orphanage;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $payloads;

    /**
     * @var int
     */
    protected $numberOfPayloads;

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDirectory);
        $filesystem->remove($this->realDirectory);
    }

    public function testUpload(): void
    {
        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount(0, $finder);
    }

    public function testUploadAndFetching(): void
    {
        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount(0, $finder);

        $files = $this->orphanage->uploadFiles();

        $this->assertIsArray($files);
        $this->assertCount($this->numberOfPayloads, $files);

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount(0, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);
    }

    public function testUploadAndFetchingIfDirectoryDoesNotExist(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDirectory);

        $files = $this->orphanage->uploadFiles();

        $this->assertIsArray($files);
        $this->assertCount(0, $files);
    }

    public function testIfGetFilesMethodIsAccessible(): void
    {
        // since ticket #48, getFiles has to be public
        $method = new \ReflectionMethod($this->orphanage, 'getFiles');
        $this->assertTrue($method->isPublic());
    }
}
