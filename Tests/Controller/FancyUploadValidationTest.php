<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class FancyUploadValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'fancyupload_validation';
    }

    protected function getRequestParameters()
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
