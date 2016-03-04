<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UniqidNamer implements NamerInterface
{
    /**
     * Name a given file and return the name
     *
     * @param  FileInterface $file
     * @param Request $request The request object.
     * 
     * @return string
     */
    public function name(FileInterface $file, Request $request)
    {
        $subfolder = '';
        if ($subfolder = $request->get('subfolder')) $subfolder .= '/';
        return sprintf('%s.%s', $subfolder.uniqid(), $file->getExtension());
    }
}
