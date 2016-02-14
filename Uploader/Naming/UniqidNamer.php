<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UniqidNamer implements NamerInterface
{
    public function name(FileInterface $file, Request $request)
    {
        $subfolder = '';
        if ($subfolder = $request->get('subfolder')) $subfolder .= '/';
        return sprintf('%s.%s', $subfolder.uniqid(), $file->getExtension());
    }
}
