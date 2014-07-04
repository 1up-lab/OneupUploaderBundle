<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;

interface OrphanageStorageInterface extends StorageInterface
{
    public function uploadFiles(array $files = null);
}
