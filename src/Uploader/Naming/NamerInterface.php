<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

interface NamerInterface
{
    /**
     * Name a given file and return the name.
     *
     * @return string
     */
    public function name(FileInterface $file);
}
