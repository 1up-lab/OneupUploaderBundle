<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadifyTest extends AbstractUploadTest
{
    protected function getConfigKey(): string
    {
        return 'uploadify';
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
