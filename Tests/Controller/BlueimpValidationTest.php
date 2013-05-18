<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractValidationTest;

class BlueimpValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'blueimp_validation';
    }
    
    protected function getRequestParameters()
    {
        return array();
    }
    
    protected function getFileWithCorrectExtension()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'text/plain',
            128
        ));
    }
    
    protected function getFileWithIncorrectExtension()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.fail',
            'text/plain',
            128
        ));
    }
    
    protected function getFileWithCorrectMimeType()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/jpg',
            128
        ));
    }
    
    protected function getFileWithIncorrectMimeType()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif',
            128
        ));
    }
}
