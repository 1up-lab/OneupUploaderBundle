<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;

interface ChunkStorageInterface
{
    public function clear(int $maxAge): void;

    /**
     * @return mixed
     */
    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original);

    /**
     * @param mixed $chunks
     *
     * @return \SplFileInfo|FlysystemFile|GaufretteFile
     */
    public function assembleChunks($chunks, bool $removeChunk, bool $renameChunk);

    public function cleanup(string $path): void;

    /**
     * @return mixed
     */
    public function getChunks(string $uuid);
}
