<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

class PluploadTest extends AbstractControllerTest
{
    protected function getConfigKey()
    {
        return 'plupload';
    }
    
    protected function getRequestParameters()
    {
        return array();
    }
    
    protected function getRequestFile()
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain',
            128
        );
    }
}
