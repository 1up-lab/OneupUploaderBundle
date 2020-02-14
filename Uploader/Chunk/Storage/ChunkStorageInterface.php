<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkStorageInterface
{
    public function clear(int $maxAge);

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, $original);

    public function assembleChunks($chunks, $removeChunk, $renameChunk);

    public function cleanup($path);

    public function getChunks($uuid);
}
