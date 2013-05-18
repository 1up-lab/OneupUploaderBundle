<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractUploadTest;

class BlueimpTest extends AbstractUploadTest
{
    protected function getConfigKey()
    {
        return 'blueimp';
    }
    
    protected function getRequestParameters()
    {
        return array();
    }
    
    protected function getRequestFile()
    {
        return array(new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain',
            128
        ));
    }
}
