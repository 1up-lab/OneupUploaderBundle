<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

interface OrphanageStorageInterface extends StorageInterface
{
    public function uploadFiles(array $files = null);
}
