<?php

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkStorageInterface
{
    public function clear($maxAge);

    public function addChunk($uuid, $index, UploadedFile $chunk, $original);

    public function assembleChunks($chunks, $removeChunk, $renameChunk);

    public function cleanup($path);

    public function getChunks($uuid);
}
