<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\File;
use League\Flysystem\Filesystem as FSAdapter;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage as ChunkStorage;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemOrphanageStorage;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemStorage as Storage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Twistor\FlysystemStreamWrapper;

class FlysystemOrphanageStorageTest extends OrphanageTest
{
    /**
     * @var string
     */
    protected $chunkDirectory;

    /**
     * @var string
     */
    protected $chunksKey = 'chunks';

    /**
     * @var string
     */
    protected $orphanageKey = 'orphanage';

    protected function setUp(): void
    {
        $this->numberOfPayloads = 5;
        $this->realDirectory = sys_get_temp_dir() . '/storage';
        $this->chunkDirectory = $this->realDirectory . '/' . $this->chunksKey;
        $this->tempDirectory = $this->realDirectory . '/' . $this->orphanageKey;
        $this->payloads = [];

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

        $config = ['directory' => 'orphanage'];

        $this->orphanage = new FlysystemOrphanageStorage($this->storage, $session, $chunkStorage, $config, 'cat');

        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            // create temporary file as if it was reassembled by the chunk manager
            $file = (string) tempnam($this->chunkDirectory, 'uploader');

            /** @var resource $pointer */
            $pointer = fopen($file, 'w+');

            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);

            //file key needs to be relative to the root of the flysystem filesystem
            // It seems that tempnam on OS X prepends 'private' to chunkDirectory, so strip that off as well
            $fileKey = str_replace([$this->realDirectory, '/private'], '', $file);

            $this->payloads[] = new FlysystemFile(new File($filesystem, $fileKey), $filesystem);
        }
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->realDirectory);

        FlysystemStreamWrapper::unregister('tests');
    }

    public function testUpload(): void
    {
        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        // exclude the orphanage and the chunks
        $finder->in($this->realDirectory)->exclude([$this->orphanageKey, $this->chunksKey])->files();
        $this->assertCount(0, $finder);
    }

    public function testUploadAndFetching(): void
    {
        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            $this->orphanage->upload($this->payloads[$i], $i . 'notsogrumpyanymore.jpeg');
        }

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->exclude([$this->orphanageKey, $this->chunksKey])->files();
        $this->assertCount(0, $finder);

        $files = $this->orphanage->uploadFiles();

        $this->assertIsArray($files);
        $this->assertCount($this->numberOfPayloads, $files);

        $finder = new Finder();
        $finder->in($this->tempDirectory)->files();
        $this->assertCount(0, $finder);

        $finder = new Finder();
        $finder->in($this->realDirectory)->files();
        $this->assertCount($this->numberOfPayloads, $finder);
    }
}
