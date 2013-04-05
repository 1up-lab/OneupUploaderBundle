<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;

interface OrphanageStorageInterface extends StorageInterface
{
    public function uploadFiles();
}
