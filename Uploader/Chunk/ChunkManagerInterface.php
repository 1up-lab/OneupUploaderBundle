<?php

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ChunkManagerInterface
{
    /**
     * Adds a new Chunk to a given uuid.
     *
     * @param string       $uuid
     * @param int          $index
     * @param UploadedFile $chunk
     * @param string       $original The file name of the original file
     *
     * @return File The moved chunk file.
     */
    public function addChunk($uuid, $index, UploadedFile $chunk, $original);

    /**
     * Assembles the given chunks and return the resulting file.
     *
     * @param      $chunks
     * @param bool $removeChunk Remove the chunk file once its assembled.
     * @param bool $renameChunk Rename the chunk file once its assembled.
     *
     * @return File
     */
    public function assembleChunks($chunks, $removeChunk = true, $renameChunk = false);

    /**
     * Get chunks associated with the given uuid.
     *
     * @param string $uuid
     *
     * @return Finder A finder instance
     */
    public function getChunks($uuid);

    /**
     * Clean a given path.
     *
     * @param  string $path
     * @return bool
     */
    public function cleanup($path);

    /**
     * Clears the chunk manager directory. Remove all files older than the configured maxage.
     *
     * @return void
     */
    public function clear();
}
