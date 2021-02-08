<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemStorageTest extends TestCase
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $file;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/storage';

        // create temporary file
        $this->file = (string) tempnam(sys_get_temp_dir(), 'uploader');

        /** @var resource $pointer */
        $pointer = fopen($this->file, 'w+');

        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);
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
        $storage = new FilesystemStorage($this->directory);
        $storage->upload($payload, 'notsogrumpyanymore.jpeg');

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }
}
