<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use League\Flysystem\Filesystem as FSAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter as Adapter;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemStorage as Storage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FlysystemStorageTest extends TestCase
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var string
     */
    protected $file;

    /** @var FSAdapter */
    protected $filesystem;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/storage';

        // create temporary file
        $this->file = (string) tempnam(sys_get_temp_dir(), 'uploader');

        /** @var resource $pointer */
        $pointer = fopen($this->file, 'w+');

        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $adapter = new Adapter($this->directory);
        $this->filesystem = new FSAdapter($adapter);

        $this->storage = new Storage($this->filesystem, 100000);
    }

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->directory);
    }

    /**
     * @throws FilesystemException
     */
    public function testUpload(): void
    {
        $uploadedFile = new UploadedFile($this->file, 'grumpycat.jpeg', null, null, true);

        $payload = new FilesystemFile($uploadedFile);
        $file = $this->storage->upload($payload, 'notsogrumpyanymore.jpeg');
        $this->assertInstanceOf(FlysystemFile::class, $file);
        $this->assertSame('notsogrumpyanymore.jpeg', $file->getPathname());
        $this->assertSame(1024, $file->getSize());

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }

    /**
     * @throws FilesystemException
     */
    public function testUploadWithPath(): void
    {
        $uploadedFile = new UploadedFile($this->file, 'grumpycat.jpeg', null, null, true);

        $payload = new FilesystemFile($uploadedFile);
        $file = $this->storage->upload($payload, 'notsogrumpyanymore.jpeg', 'cat');
        $this->assertInstanceOf(FlysystemFile::class, $file);
        $this->assertSame('cat/notsogrumpyanymore.jpeg', $file->getPathname());
        $this->assertSame(1024, $file->getSize());

        $finder = new Finder();
        $finder->in($this->directory)->files();

        $this->assertCount(1, $finder);

        foreach ($finder as $file) {
            $this->assertStringEndsWith('cat', $file->getPath());
            $this->assertSame($file->getFilename(), 'notsogrumpyanymore.jpeg');
            $this->assertSame($file->getSize(), 1024);
        }
    }

    public function testUploadFlysystemFile(): void
    {
        $tempFileName = basename($this->file);
        $localPath = $this->directory . '/' . $tempFileName;
        $flysystemFile = new FlysystemFile($tempFileName, $this->filesystem);
        copy($this->file, $localPath);
        $this->assertFileExists($localPath);

        $file = $this->storage->upload($flysystemFile, 'final.jpg');
        $this->assertInstanceOf(FlysystemFile::class, $file);
        $this->assertSame('final.jpg', $file->getPathname());
        $this->assertSame(1024, $file->getSize());

        $this->assertFileDoesNotExist($localPath);
        $this->assertFileExists($this->directory . '/final.jpg');
    }

    public function testUploadFlysystemFileFromDifferentFilesystem(): void
    {
        $adapter = new Adapter(sys_get_temp_dir());
        $filesystem = new FSAdapter($adapter);

        $flysystemFile = new FlysystemFile(basename($this->file), $filesystem);

        $file = $this->storage->upload($flysystemFile, 'final.jpg');
        $this->assertInstanceOf(FlysystemFile::class, $file);
        $this->assertSame('final.jpg', $file->getPathname());
        $this->assertSame(1024, $file->getSize());

        $this->assertFileDoesNotExist($this->file);
        $this->assertFileExists($this->directory . '/final.jpg');
    }
}
