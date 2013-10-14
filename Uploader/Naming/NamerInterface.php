<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

interface NamerInterface
{
    /**
     * Name a given file and return the name
     *
     * @param  FileInterface $file
     * @return string
     */
    public function name(FileInterface $file);
}
