<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
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
    public function upload(FileInterface $file, $name, $path = null);
}
