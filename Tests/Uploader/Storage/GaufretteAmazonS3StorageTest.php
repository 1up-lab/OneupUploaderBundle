<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use \AmazonS3 as AmazonClient;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Gaufrette\Adapter\AmazonS3 as S3Adapter;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;

class GaufretteAmazonS3StorageTest extends \PHPUnit_Framework_TestCase
{
    protected $directory;
    protected $storage;

    public function setUp()
    {
        if(
            "" == getenv('AWS_ACCESS_KEY_ID') ||
            "" == getenv('AWS_SECRET_ACCESS_KEY') ||
            "" == getenv('AWS_BUCKET')
        ) {
            $this->markTestSkipped('Missing AWS_* ENV variables.');
        }

        $this->prefix = 'someObscureStorage';
        $this->directory = sys_get_temp_dir() .'/'. $this->prefix;
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }

        // create temporary file
        $this->file = tempnam($this->directory, 'uploader');

        $pointer = fopen($this->file, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);

        $service = new AmazonClient(array(
          'key' => getenv('AWS_ACCESS_KEY_ID'),
          'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        ));
        $adapter = new S3Adapter($service, getenv('AWS_BUCKET'));
        $this->filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($this->filesystem);
    }

    public function testUpload()
    {
        $payload = new UploadedFile($this->file, 'grumpycat.jpeg', null, null, null, true);
        $this->storage->upload($payload, $this->prefix .'/notsogrumpyanymore.jpeg');

        $files = $this->filesystem->listKeys($this->prefix);

        // on Amazon S3, if it exists, it is considered a directory
        $this->assertCount(2, $files['keys']);

        foreach ($files['keys'] as $filename) {
          if ($filename === $this->prefix) {
            // ignore the prefix directory
            continue;
          }
          $this->assertEquals($this->prefix. '/notsogrumpyanymore.jpeg', $filename);
          $this->assertEquals(1024, strlen($this->filesystem->read($filename)));
        }
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
}
