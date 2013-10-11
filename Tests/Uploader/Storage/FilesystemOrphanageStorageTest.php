<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemOrphanageStorage;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage as FilesystemChunkStorage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;

class FilesystemOrphanageStorageTest extends OrphanageTest
{
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

            $this->payloads[] = new FilesystemFile(new UploadedFile($file, $i . 'grumpycat.jpeg', null, null, null, true));
        }

        // create underlying storage
        $this->storage = new FilesystemStorage($this->realDirectory);
        // is ignored anyways
        $chunkStorage = new FilesystemChunkStorage('/tmp/');

        // create orphanage
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $config = array('directory' => $this->tempDirectory);

        $this->orphanage = new FilesystemOrphanageStorage($this->storage, $session, $chunkStorage, $config, 'cat');
    }
}
