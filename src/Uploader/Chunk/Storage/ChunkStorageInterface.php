<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkStorageInterface
{
    public function clear(int $maxAge): void;

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original): ?FileInterface;

    public function assembleChunks(\IteratorAggregate|iterable|null $chunks, bool $removeChunk, bool $renameChunk): FileInterface;

    public function cleanup(string $path): void;

    public function getChunks(string $uuid): array;
}
