<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DropzoneValidationTest extends AbstractValidationTest
{
    protected function getConfigKey(): string
    {
        return 'dropzone_validation';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getOversizedFile()
    {
        return new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeType()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeTypeAndIncorrectExtension()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txxt',
            'text/plain'
        );
    }

    protected function getFileWithIncorrectMimeType()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif'
        );
    }
}
