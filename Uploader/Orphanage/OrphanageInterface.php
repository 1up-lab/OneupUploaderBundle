<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Symfony\Component\HttpFoundation\File\File;

interface OrphanageInterface
{
    public function addFile(File $file, $name);
}
