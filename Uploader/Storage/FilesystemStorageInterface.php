<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;

interface FilesystemStorageInterface
{
    /**
     * Uploads a File instance to the configured storage.
     * Requires local files, it doesn't make sense to upload to
     * a graufrette filesystem first, and then move it to a local one.
     *
     * @param FilesystemFile $file
     * @param string         $name
     * @param string         $path
     */
    public function upload(FilesystemFile $file, $name, $path = null);
}
