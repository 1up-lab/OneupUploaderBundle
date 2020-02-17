<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class YUI3ValidationTest extends AbstractValidationTest
{
    protected function getConfigKey(): string
    {
        return 'yui3_validation';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getOversizedFile(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeType(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeTypeAndIncorrectExtension(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txxt',
            'text/plain'
        );
    }

    protected function getFileWithIncorrectMimeType(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif'
        );
    }
}
