<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

interface NamerInterface
{
    /**
     * Name a given file and return the name
     *
     * @param  FileInterface $file
     * @return string
     */
    public function name(FileInterface $file, Request $request);
}
