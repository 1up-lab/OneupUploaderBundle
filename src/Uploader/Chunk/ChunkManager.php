<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChunkManager implements ChunkManagerInterface
{
    public function __construct(protected array $configuration, protected ChunkStorageInterface $storage)
    {
    }

    public function clear(): void
    {
        $this->storage->clear($this->configuration['maxage']);
    }

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original): mixed
    {
        return $this->storage->addChunk($uuid, $index, $chunk, $original);
    }

    public function assembleChunks(mixed $chunks, bool $removeChunk = true, bool $renameChunk = false): mixed
    {
        return $this->storage->assembleChunks($chunks, $removeChunk, $renameChunk);
    }

    public function cleanup(string $path): void
    {
        $this->storage->cleanup($path);
    }

    public function getChunks(string $uuid): mixed
    {
        return $this->storage->getChunks($uuid);
    }

    public function getLoadDistribution(): bool
    {
        return $this->configuration['load_distribution'];
    }
}
