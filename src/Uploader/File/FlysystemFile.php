<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\File;

// TODO V2
use League\Flysystem\FilesystemInterface;

class FlysystemFile implements FileInterface
{
    /** @var int */
    private $size;

    /** @var string */
    private $pathname;

    /** @var string */
    private $mimeType;

    /** @var FilesystemInterface */
    private $filesystem;

    public function __construct(string $pathname, int $size, string $mimeType, FilesystemInterface $filesystem)
    {
        $this->pathname = $pathname;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->filesystem = $filesystem;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getPath(): string
    {
        return pathinfo($this->pathname, \PATHINFO_DIRNAME);
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getBasename(): string
    {
        return basename($this->pathname);
    }

    public function getExtension(): string
    {
        return pathinfo($this->pathname, \PATHINFO_EXTENSION);
    }

    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }
}
