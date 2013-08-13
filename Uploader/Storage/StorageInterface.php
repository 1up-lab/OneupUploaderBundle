<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;

interface StorageInterface
{
    /**
     * Uploads a File instance to the configured storage.
     *
     * @param File   $file
     * @param string $name
     * @param string $path
     */
    public function upload(File $file, $name, $path = null);
}
