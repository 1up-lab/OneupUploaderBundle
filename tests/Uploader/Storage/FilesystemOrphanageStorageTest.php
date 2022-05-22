<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage as FilesystemChunkStorage;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemOrphanageStorage;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FilesystemOrphanageStorageTest extends OrphanageTest
{
    protected function setUp(): void
    {
        $this->numberOfPayloads = 5;
        $this->tempDirectory = sys_get_temp_dir() . '/orphanage';
        $this->realDirectory = sys_get_temp_dir() . '/storage';
        $this->payloads = [];

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tempDirectory);
        $filesystem->mkdir($this->realDirectory);

        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            // create temporary file
            $file = (string) tempnam(sys_get_temp_dir(), 'uploader');

            /** @var resource $pointer */
            $pointer = fopen($file, 'w+');

            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);

            $this->payloads[] = new FilesystemFile(new UploadedFile($file, $i . 'grumpycat.jpeg', null, null, true));
        }

        // create underlying storage
        $this->storage = new FilesystemStorage($this->realDirectory);

        // is ignored anyways
        $chunkStorage = new FilesystemChunkStorage('/tmp/');

        // create orphanage
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($session);
        $requestStack->push($request);

        $config = ['directory' => $this->tempDirectory];

        $this->orphanage = new FilesystemOrphanageStorage($this->storage, $requestStack, $chunkStorage, $config, 'cat');
    }
}
