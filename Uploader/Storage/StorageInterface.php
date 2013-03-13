<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\Finder\SplFileInfo as File;

interface StorageInterface
{
    public function upload(\SplFileInfo $file, $name);
    public function remove(File $file);
}