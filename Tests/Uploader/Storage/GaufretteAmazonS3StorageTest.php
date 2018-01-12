<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use AmazonS3 as AmazonClient;
use Gaufrette\Adapter\AmazonS3 as S3Adapter;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GaufretteAmazonS3StorageTest extends TestCase
{
    protected $directory;
    protected $storage;

    public function setUp()
    {
        if (
            false === getenv('AWS_ACCESS_KEY_ID') ||
            false === getenv('AWS_SECRET_ACCESS_KEY') ||
            false === getenv('AWS_BUCKET')
        ) {
            $this->markTestSkipped('Missing AWS_* ENV variables.');
        }

        $this->prefix = 'someObscureStorage';
        $this->directory = sys_get_temp_dir().'/'.$this->prefix;
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }

        // create temporary file
        $this->file = tempnam($this->directory, 'uploader');

        $pointer = fopen($this->file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $service = new AmazonClient([
          'key' => getenv('AWS_ACCESS_KEY_ID'),
          'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        ]);
        $adapter = new S3Adapter($service, getenv('AWS_BUCKET'));
        $this->filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($this->filesystem, 100000, null);
    }

    public function tearDown()
    {
        $files = $this->filesystem->listKeys($this->prefix);
        foreach ($files['keys'] as $filename) {
            if ($this->filesystem->has($filename)) {
                $this->filesystem->delete($filename);
            }
        }
    }

    public function testUpload()
    {
        $payload = new FilesystemFile(new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true));
        $this->storage->upload($payload, $this->prefix.'/notsogrumpyanymore.jpeg');

        $files = $this->filesystem->listKeys($this->prefix);

        // on Amazon S3, if it exists, it is considered a directory
        $this->assertCount(2, $files['keys']);

        foreach ($files['keys'] as $filename) {
            if ($filename === $this->prefix) {
                // ignore the prefix directory
                continue;
            }
            $this->assertSame($this->prefix.'/notsogrumpyanymore.jpeg', $filename);
            $this->assertSame(1024, strlen($this->filesystem->read($filename)));
        }
    }
}
