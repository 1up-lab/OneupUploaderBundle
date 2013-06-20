<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface NamerInterface
{
    public function name(UploadedFile $file);
}
