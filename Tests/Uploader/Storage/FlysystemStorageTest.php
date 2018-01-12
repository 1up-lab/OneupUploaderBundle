<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem as FSAdapter;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemStorage as Storage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FlysystemStorageTest extends TestCase
{
    protected $directory;

    /**
     * @var Storage
     */
    protected $storage;
    protected $file;

    public function setUp()
    {
        $this->directory = sys_get_temp_dir().'/storage';

        // create temporary file
        $this->file = tempnam(sys_get_temp_dir(), 'uploader');

        $pointer = fopen($this->file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $adapter = new Adapter($this->directory, true);
        $filesystem = new FSAdapter($adapter);

        $this->storage = new Storage($filesystem, 100000);
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->directory);
    }

    public function testUpload()
    {
        $payload = new FilesystemFile(new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true));
        $this->storage->upload($payload, 'notsogrumpyanymore.jpeg');

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }

    public function testUploadWithPath()
    {
        $payload = new FilesystemFile(new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true));
        $this->storage->upload($payload, 'notsogrumpyanymore.jpeg', 'cat');

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }
}
