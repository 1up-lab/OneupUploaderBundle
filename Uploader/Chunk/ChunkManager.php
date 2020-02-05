<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChunkManager implements ChunkManagerInterface
{
    protected $configuration;
    protected $storage;

    public function __construct($configuration, ChunkStorageInterface $storage)
    {
        $this->configuration = $configuration;
        $this->storage = $storage;
    }

    public function clear(): void
    {
        $this->storage->clear($this->configuration['maxage']);
    }

    public function addChunk($uuid, $index, UploadedFile $chunk, $original)
    {
        return $this->storage->addChunk($uuid, $index, $chunk, $original);
    }

    public function assembleChunks($chunks, $removeChunk = true, $renameChunk = false)
    {
        return $this->storage->assembleChunks($chunks, $removeChunk, $renameChunk);
    }

    public function cleanup($path)
    {
        return $this->storage->cleanup($path);
    }

    public function getChunks($uuid)
    {
        return $this->storage->getChunks($uuid);
    }

    public function getLoadDistribution()
    {
        return $this->configuration['load_distribution'];
    }
}
