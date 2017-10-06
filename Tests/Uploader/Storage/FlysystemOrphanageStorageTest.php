<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use League\Flysystem\File;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage as ChunkStorage;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemOrphanageStorage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem as FSAdapter;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemStorage as Storage;
use Twistor\FlysystemStreamWrapper;

class FlysystemOrphanageStorageTest extends OrphanageTest
{
    protected $chunkDirectory;
    protected $chunksKey = 'chunks';
    protected $orphanageKey = 'orphanage';

    public function setUp()
    {
        $this->numberOfPayloads = 5;
        $this->realDirectory = sys_get_temp_dir() . '/storage';
        $this->chunkDirectory = $this->realDirectory .'/' . $this->chunksKey;
        $this->tempDirectory = $this->realDirectory . '/' . $this->orphanageKey;
        $this->payloads = array();

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->realDirectory);
        $filesystem->mkdir($this->chunkDirectory);
        $filesystem->mkdir($this->tempDirectory);

        $adapter = new Adapter($this->realDirectory);
        $filesystem = new FSAdapter($adapter);

        FlysystemStreamWrapper::register('tests', $filesystem);

        $this->storage = new Storage($filesystem, 100000);

        $chunkStorage = new ChunkStorage($filesystem, 100000, 'tests:/', 'chunks');

        // create orphanage
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $config = array('directory' => 'orphanage');

        $this->orphanage = new FlysystemOrphanageStorage($this->storage, $session, $chunkStorage, $config, 'cat');

        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            // create temporary file as if it was reassembled by the chunk manager
            $file = tempnam($this->chunkDirectory, 'uploader');

            $pointer = fopen($file, 'w+');
            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);

            //file key needs to be relative to the root of the flysystem filesystem
            $fileKey = str_replace($this->realDirectory, '', $file);

            $this->payloads[] = new FlysystemFile(new File($filesystem, $fileKey), $filesystem);
        }
    }

    public function tearDown()
    {
        (new Filesystem())->remove($this->realDirectory);

        FlysystemStreamWrapper::unregister('tests');
    }

    public function testUpload()
    {
        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        // exclude the orphanage and the chunks
        $finder->in($this->realDirectory)->exclude(array($this->orphanageKey, $this->chunksKey))->files();
        $this->assertCount(0, $finder);
    }

    public function testUploadAndFetching()
    {
        for ($i = 0; $i < $this->numberOfPayloads; $i ++) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->exclude(array($this->orphanageKey, $this->chunksKey))->files();
        $this->assertCount(0, $finder);

        $files = $this->orphanage->uploadFiles();

        $this->assertTrue(is_array($files));
        $this->assertCount($this->numberOfPayloads, $files);

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount(0, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);
    }
}
