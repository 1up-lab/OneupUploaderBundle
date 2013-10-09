<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemFile extends UploadedFile implements FileInterface
{
    protected $file;
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        parent::__construct($file->getPath(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getClientSize(), $file->getError(), true);
    }
} 