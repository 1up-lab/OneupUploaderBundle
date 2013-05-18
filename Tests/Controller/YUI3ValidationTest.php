<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractUploadTest;

class YUI3ValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'yui3_validation';
    }
    
    protected function getRequestParameters()
    {
        return array();
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
