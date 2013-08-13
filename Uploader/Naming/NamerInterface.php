<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface NamerInterface
{
    /**
     * Name a given file and return the name
     *
     * @param  UploadedFile $file
     * @return string
     */
    public function name(UploadedFile $file);
}
