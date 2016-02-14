<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UniqidNamer implements NamerInterface
{
    public function name(FileInterface $file, $request)
    {
        return sprintf('%s.%s', uniqid(), $file->getExtension());
    }
}
