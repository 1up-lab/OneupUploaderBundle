<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use League\Flysystem\File;
use Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage as ChunkStorage;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlysystemOrphanageStorage extends FlysystemStorage implements OrphanageStorageInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var ChunkStorage
     */
    protected $chunkStorage;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $type;

    public function __construct(StorageInterface $storage, SessionInterface $session, ChunkStorage $chunkStorage, array $config, string $type)
    {
        /*
         * initiate the storage on the chunk storage's filesystem
         * the stream wrapper is useful for metadata.
         */
        parent::__construct($chunkStorage->getFilesystem(), $chunkStorage->bufferSize, $chunkStorage->getStreamWrapperPrefix());

        $this->storage = $storage;
        $this->chunkStorage = $chunkStorage;
        $this->session = $session;
        $this->config = $config;
        $this->type = $type;
    }

    /**
     * @param FileInterface|SymfonyFile $file
     *
     * @return FileInterface|SymfonyFile
     */
    public function upload($file, string $name, string $path = null)
    {
        if (!$this->session->isStarted()) {
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');
        }

        return parent::upload($file, $name, $this->getPath());
    }

    public function uploadFiles(array $files = null): array
    {
        try {
            if (null === $files) {
                $files = $this->getFiles();
            }
            $return = [];

            foreach ($files as $key => $file) {
                try {
                    $return[] = $this->storage->upload($file, str_replace($this->getPath(), '', $key));
                } catch (\Exception $e) {
                    // well, we tried.
                    continue;
                }
            }

            return $return;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getFiles(): array
    {
        $fileList = $this->chunkStorage
            ->getFilesystem()
            ->listContents($this->getPath());
        $files = [];

        foreach ($fileList as $fileDetail) {
            $key = $fileDetail['path'];
            if ('file' === $fileDetail['type']) {
                $files[$key] = new FlysystemFile(
                    new File($this->chunkStorage->getFilesystem(), $key),
                    $this->chunkStorage->getFilesystem()
                );
            }
        }

        return $files;
    }

    protected function getPath(): string
    {
        // the storage is initiated in the root of the filesystem, from where the orphanage directory
        // should be relative.
        return sprintf('%s/%s/%s', $this->config['directory'], $this->session->getId(), $this->type);
    }
}
