<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemFile extends UploadedFile implements FileInterface
{
    public function __construct(File $file)
    {
        if ($file instanceof UploadedFile) {
            parent::__construct($file->getPathname(), (string) $file->getClientOriginalName(), $file->getClientMimeType(), $file->getError(), true);
        } else {
            parent::__construct($file->getPathname(), $file->getBasename(), $file->getMimeType(), 0, true);
        }
    }

    public function getExtension(): string
    {
        return $this->getClientOriginalExtension();
    }

    public function getFileSystem()
    {
        return null;
    }
}
