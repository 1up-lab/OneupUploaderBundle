<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

interface StorageInterface
{
    /**
     * Uploads a File instance to the configured storage.
     *
     * @param        $file
     * @param string $name
     * @param string $path
     */
    public function upload(FileInterface $file, $name, $path = null);
}
