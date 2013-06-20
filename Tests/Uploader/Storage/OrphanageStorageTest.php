<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorage;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;

class OrphanageStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $tempDirectory;
    protected $realDirectory;
    protected $orphanage;
    protected $storage;
    protected $payloads;
    protected $numberOfPayloads;

    public function setUp()
    {
        $this->numberOfPayloads = 5;
        $this->tempDirectory = sys_get_temp_dir() . '/orphanage';
        $this->realDirectory = sys_get_temp_dir() . '/storage';
        $this->payloads = array();

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tempDirectory);
        $filesystem->mkdir($this->realDirectory);

        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            // create temporary file
            $file = tempnam(sys_get_temp_dir(), 'uploader');

            $pointer = fopen($file, 'w+');
            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);

            $this->payloads[] = new UploadedFile($file, $i . 'grumpycat.jpeg', null, null, null, true);
        }

        // create underlying storage
        $this->storage = new FilesystemStorage($this->realDirectory);

        // create orphanage
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $config = array('directory' => $this->tempDirectory);

        $this->orphanage = new OrphanageStorage($this->storage, $session, $config, 'cat');
    }

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

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDirectory);
        $filesystem->remove($this->realDirectory);
    }
}
