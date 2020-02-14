<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class FilesystemFile extends UploadedFile implements FileInterface
{
    public function __construct(File $file)
    {
        if ($file instanceof UploadedFile) {
            // TODO at EOL of SF 3.4 this can be removed
            if(Kernel::VERSION_ID < 40400) {
                parent::__construct($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getSize(), $file->getError(), true);
            } else {
                parent::__construct($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getError(), true);
            }
        } else {
            // TODO at EOL of SF 3.4 this can be removed
            if(Kernel::VERSION_ID < 40400) {
                parent::__construct($file->getPathname(), $file->getBasename(), $file->getMimeType(), $file->getSize(), 0, true);
            } else {
                parent::__construct($file->getPathname(), $file->getBasename(), $file->getMimeType(), 0, true);
            }
        }
    }

    public function getExtension()
    {
        return $this->getClientOriginalExtension();
    }
}
