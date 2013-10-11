<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

abstract class OrphanageTest extends \PHPUnit_Framework_Testcase
{
    protected $tempDirectory;
    protected $realDirectory;
    protected $orphanage;
    protected $storage;
    protected $payloads;
    protected $numberOfPayloads;

    public function testUpload()
    {
        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount(0, $finder);
    }

    public function testUploadAndFetching()
    {
        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount(0, $finder);

        $files = $this->orphanage->uploadFiles();

        $this->assertTrue(is_array($files));
        $this->assertCount($this->numberOfPayloads, $files);

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount(0, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);
    }

    public function testUploadAndFetchingIfDirectoryDoesNotExist()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDirectory);

        $files = $this->orphanage->uploadFiles();

        $this->assertTrue(is_array($files));
        $this->assertCount(0, $files);
    }

    public function testIfGetFilesMethodIsAccessible()
    {
        // since ticket #48, getFiles has to be public
        $method = new \ReflectionMethod($this->orphanage, 'getFiles');
        $this->assertTrue($method->isPublic());
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDirectory);
        $filesystem->remove($this->realDirectory);
    }
}
