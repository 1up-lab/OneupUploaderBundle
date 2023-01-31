<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChunkManager implements ChunkManagerInterface
{
    protected array $configuration;

    protected ChunkStorageInterface $storage;

    public function __construct(array $configuration, ChunkStorageInterface $storage)
    {
        $this->configuration = $configuration;
        $this->storage = $storage;
    }

    public function clear(): void
    {
        $this->storage->clear($this->configuration['maxage']);
    }

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original): ?FileInterface
    {
        return $this->storage->addChunk($uuid, $index, $chunk, $original);
    }

    public function assembleChunks(array $chunks, $removeChunk = true, $renameChunk = false): FileInterface
    {
        return $this->storage->assembleChunks($chunks, $removeChunk, $renameChunk);
    }

    public function cleanup(?string $path): void
    {
        if (null !== $path) {
            $this->storage->cleanup($path);
        }
    }

    public function getChunks(string $uuid): array
    {
        return $this->storage->getChunks($uuid);
    }

    public function getLoadDistribution(): bool
    {
        return $this->configuration['load_distribution'];
    }
}
