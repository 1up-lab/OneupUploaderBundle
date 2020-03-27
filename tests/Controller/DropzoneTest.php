<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DropzoneTest extends AbstractUploadTest
{
    protected function getConfigKey(): string
    {
        return 'dropzone';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getRequestFile(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        );
    }
}
