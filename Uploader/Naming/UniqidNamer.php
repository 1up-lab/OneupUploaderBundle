<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UniqidNamer implements NamerInterface
{
    public function name(UploadedFile $file, $prefix = null)
    {
        $prefix = !is_null($prefix) ? $prefix . '/' : '';
         
        return sprintf('%s%s.%s', $prefix, uniqid(), $file->guessExtension());
    }
}