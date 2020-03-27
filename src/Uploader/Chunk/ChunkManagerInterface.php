<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkManagerInterface
{
    /**
     * Adds a new Chunk to a given uuid.
     *
     * @return mixed
     */
    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original);

    /**
     * Assembles the given chunks and return the resulting file.
     *
     * @param mixed $chunks
     * @param bool  $removeChunk remove the chunk file once its assembled
     * @param bool  $renameChunk rename the chunk file once its assembled
     *
     * @return mixed
     */
    public function assembleChunks($chunks, $removeChunk = true, $renameChunk = false);

    /**
     * Get chunks associated with the given uuid.
     *
     * @return mixed
     */
    public function getChunks(string $uuid);

    /**
     * Clean a given path.
     */
    public function cleanup(string $path): void;

    /**
     * Clears the chunk manager directory. Remove all files older than the configured maxage.
     */
    public function clear(): void;

    public function getLoadDistribution(): bool;
}
