<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

interface OrphanageStorageInterface extends StorageInterface
{
    public function uploadFiles(array $files = null): array;
}
