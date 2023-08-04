<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkStorageInterface
{
    public function clear(int $maxAge): void;

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original);

    public function assembleChunks($chunks, bool $removeChunk, bool $renameChunk);

    public function cleanup(string $path): void;

    public function getChunks(string $uuid);
}
