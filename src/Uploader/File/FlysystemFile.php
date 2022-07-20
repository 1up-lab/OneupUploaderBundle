<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\File;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

class FlysystemFile implements FileInterface
{
    /** @var string */
    private $pathname;

    /** @var FilesystemOperator */
    private $filesystem;

    public function __construct(string $pathname, FilesystemOperator $filesystem)
    {
        $this->pathname = $pathname;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws FilesystemException
     */
    public function getSize(): int
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
    public function getMimeType(): string
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
