<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\File;

use League\Flysystem\File;
use League\Flysystem\FilesystemInterface;

class FlysystemFile extends File implements FileInterface
{
    /**
     * @var string|null
     */
    protected $streamWrapperPrefix;

    /**
     * @var string
     */
    protected $mimeType;

    public function __construct(File $file, FilesystemInterface $filesystem, string $streamWrapperPrefix = null)
    {
        parent::__construct($filesystem, $file->getPath());

        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function getPathname(): string
    {
        return $this->getPath();
    }

    public function getBasename(): string
    {
        return pathinfo($this->getPath(), \PATHINFO_BASENAME);
    }

    public function getExtension(): string
    {
        return pathinfo($this->getPath(), \PATHINFO_EXTENSION);
    }

    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }
}
