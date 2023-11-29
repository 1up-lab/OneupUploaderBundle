<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\File;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

class FlysystemFile implements FileInterface
{
    public function __construct(private string $pathname, private FilesystemOperator $filesystem)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function getSize()
    {
        return $this->filesystem->fileSize($this->pathname);
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getPath(): string
    {
        return pathinfo($this->pathname, \PATHINFO_DIRNAME);
    }

    /**
     * @throws FilesystemException
     */
    public function getMimeType()
    {
        return $this->filesystem->mimeType($this->pathname);
    }

    public function getBasename(): string
    {
        return basename($this->pathname);
    }

    public function getExtension(): string
    {
        return pathinfo($this->pathname, \PATHINFO_EXTENSION);
    }

    public function getFilesystem(): FilesystemOperator
    {
        return $this->filesystem;
    }
}
