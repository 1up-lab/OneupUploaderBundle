<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractValidationTest;

class FineUploaderValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'fineuploader_validation';
    }

    protected function getRequestParameters()
    {
        return array();
    }

    protected function getOversizedFile()
    {
        return new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain',
            512
        );
    }

    protected function getFileWithCorrectExtension()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'text/plain',
            128
        );
    }

    protected function getFileWithIncorrectExtension()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.fail',
            'text/plain',
            128
        );
    }

    protected function getFileWithCorrectMimeType()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/jpg',
            128
        );
    }

    protected function getFileWithIncorrectMimeType()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif',
            128
        );
    }
}
