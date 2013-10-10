<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemFile extends UploadedFile implements FileInterface
{
    public function __construct(File $file)
    {
        if ($file instanceof UploadedFile) {
            parent::__construct($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getClientSize(), $file->getError(), true);
        } else {
            parent::__construct($file->getPathname(), $file->getBasename(), $file->getMimeType(), $file->getSize(), 0, true);
        }

    }

    public function getExtension()
    {
        return $this->getClientOriginalExtension();
    }
}
