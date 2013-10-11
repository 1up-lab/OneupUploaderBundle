<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;

class FilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $directory;
    protected $file;

    public function setUp()
    {
        $this->directory = sys_get_temp_dir() . '/storage';

        // create temporary file
        $this->file = tempnam(sys_get_temp_dir(), 'uploader');

        $pointer = fopen($this->file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);
    }

    public function testUpload()
    {
        $payload = new FilesystemFile(new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true));
        $storage = new FilesystemStorage($this->directory);
        $storage->upload($payload, 'notsogrumpyanymore.jpeg');

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
