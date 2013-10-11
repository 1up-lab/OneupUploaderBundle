<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Gaufrette\Adapter\Local as Adapter;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;

class GaufretteStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $directory;
    protected $storage;

    public function setUp()
    {
        $this->directory = sys_get_temp_dir() . '/storage';

        // create temporary file
        $this->file = tempnam(sys_get_temp_dir(), 'uploader');

        $pointer = fopen($this->file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $adapter = new Adapter($this->directory, true);
        $filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($filesystem, 100000);
    }

    public function testUpload()
    {
        $payload = new FilesystemFile(new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true));
        $this->storage->upload($payload, 'notsogrumpyanymore.jpeg');

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertEquals($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertEquals($file->getSize(), 1024);
        }
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->directory);
    }
}
