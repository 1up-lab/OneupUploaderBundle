<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

interface StorageInterface
{
    /**
     * Uploads a File instance to the configured storage.
     *
     * @param string $name
     * @param string $path
     */
    public function upload(FileInterface $file, $name, $path = null);
}
