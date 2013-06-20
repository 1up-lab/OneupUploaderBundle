<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;

class FilesystemStorage implements StorageInterface
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function upload(File $file, $name, $path = null)
    {
        $filesystem = new Filesystem();

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
