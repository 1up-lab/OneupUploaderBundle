<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UniqidNamer implements NamerInterface
{
    public function name(UploadedFile $file)
    {
        return sprintf('%s.%s', uniqid(), $file->guessExtension());
    }
}
