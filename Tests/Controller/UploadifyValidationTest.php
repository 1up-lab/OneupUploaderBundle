<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractValidationTest;

class UploadifyValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'uploadify_validation';
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
