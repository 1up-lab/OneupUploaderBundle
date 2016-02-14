<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UniqidNamer implements NamerInterface
{
    public function name(FileInterface $file, $request)
    {
        $subfolder = '';
        if ($subfolder = $request->get('subfolder')) $subfolder .= '/';
        return sprintf('%s.%s', $subfolder.uniqid(), $file->getExtension());
    }
}
