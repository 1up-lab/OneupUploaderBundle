<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemFile extends UploadedFile implements FileInterface
{
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getClientSize(), $file->getError(), true);

    }

    public function getExtension()
    {
        // If the file is in tmp, it has no extension, but the wrapper object
        // will have the original extension, otherwise it is better to rely
        // on the actual extension
        return parent::getExtension() ? :$this->getClientOriginalExtension();
    }
}
