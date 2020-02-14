<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemStorage implements StorageInterface
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);
        $path = sprintf('%s/%s', $this->directory, $path);

        // now that we have the correct path, compute the correct name
        // and target directory
        $targetName = basename($path);
        $targetDir = \dirname($path);

        if ($file instanceof UploadedFile) {
            $file = $file->move($targetDir, $targetName);
        }

        return $file;
    }
}
