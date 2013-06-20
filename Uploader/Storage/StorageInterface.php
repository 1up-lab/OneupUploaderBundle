<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;

interface StorageInterface
{
    public function upload(File $file, $name, $path = null);
}
