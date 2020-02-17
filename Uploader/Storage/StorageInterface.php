<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\File;

interface StorageInterface
{
    /**
     * Uploads a File instance to the configured storage.
     *
     * @param FileInterface|File $file
     *
     * @return FileInterface|File
     */
    public function upload($file, string $name, string $path = null);
}
