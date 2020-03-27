<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UniqidNamer implements NamerInterface
{
    public function name(FileInterface $file)
    {
        return sprintf('%s.%s', uniqid(), $file->getExtension());
    }
}
