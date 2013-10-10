<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\File;

class FilesystemStorage implements StorageInterface
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        if (!($file instanceof File)) {
            throw new \InvalidArgumentException('file must be an instance of Symfony\Component\HttpFoundation\File\File');
        }

        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);
        $path = sprintf('%s/%s', $this->directory, $path);

        // now that we have the correct path, compute the correct name
        // and target directory
        $targetName = basename($path);
        $targetDir  = dirname($path);

        $file = $file->move($targetDir, $targetName);

        return $file;
    }
}
