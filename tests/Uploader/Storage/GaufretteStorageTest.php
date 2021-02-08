<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Gaufrette\Adapter\Local as Adapter;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GaufretteStorageTest extends TestCase
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var GaufretteStorage
     */
    protected $storage;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/storage';

        // create temporary file
        $this->file = (string) tempnam(sys_get_temp_dir(), 'uploader');

        /** @var resource $pointer */
        $pointer = fopen($this->file, 'w+');

        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $adapter = new Adapter($this->directory, true);
        $filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($filesystem, 100000);
    }

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->directory);
    }

    public function testUpload(): void
    {
        $uploadedFile = new UploadedFile($this->file, 'grumpycat.jpeg', null, null, true);

        $payload = new FilesystemFile($uploadedFile);
        $this->storage->upload($payload, 'notsogrumpyanymore.jpeg');

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }
}
