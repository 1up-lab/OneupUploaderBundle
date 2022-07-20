<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Storage;

use Gaufrette\Adapter\Local as Adapter;
use Gaufrette\File;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\GaufretteStorage as GaufretteChunkStorage;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteOrphanageStorage;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class GaufretteOrphanageStorageTest extends OrphanageTest
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

        if (!$this->checkIfTempnameMatchesAfterCreation()) {
            $this->markTestSkipped('Temporary directories do not match');
        }

        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        $filesystem->mkdir($this->realDirectory);
        $filesystem->mkdir($this->chunkDirectory);
        $filesystem->mkdir($this->tempDirectory);

        $adapter = new Adapter($this->realDirectory, true);
        $filesystem = new GaufretteFilesystem($adapter);

        $this->storage = new GaufretteStorage($filesystem, 100000);

        $chunkStorage = new GaufretteChunkStorage($filesystem, 100000, null, 'chunks');

        // create orphanage
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($session);
        $requestStack->push($request);

        $config = ['directory' => 'orphanage'];

        $this->orphanage = new GaufretteOrphanageStorage($this->storage, $requestStack, $chunkStorage, $config, 'cat');

        for ($i = 0; $i < $this->numberOfPayloads; ++$i) {
            // create temporary file as if it was reassembled by the chunk manager
            $file = (string) tempnam($this->chunkDirectory, 'uploader');

            /** @var resource $pointer */
            $pointer = fopen($file, 'w+');

            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);

            //gaufrette needs the key relative to it's root
            $fileKey = str_replace($this->realDirectory, '', $file);

            $this->payloads[] = new GaufretteFile(new File($fileKey, $filesystem), $filesystem);
        }
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

    public function checkIfTempnameMatchesAfterCreation(): bool
    {
        return 0 === strpos((string) @tempnam($this->chunkDirectory, 'uploader'), $this->chunkDirectory);
    }
}
