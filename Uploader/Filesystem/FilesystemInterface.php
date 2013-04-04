<?php

namespace Oneup\UploaderBundle\Uploader\Filesystem;

interface FilesystemInterface
{
    public function upload(File $file, $name = null, $path = null);
}