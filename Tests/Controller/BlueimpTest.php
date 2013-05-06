<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

class BlueimpTest extends AbstractControllerTest
{
    protected function getConfigKey()
    {
        return 'blueimp';
    }
    
    protected function getSingleRequestParameters()
    {
        return array();
    }
    
    protected function getSingleRequestFile()
    {
        $file = new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain',
            128
        );
        
        return array($file);
    }
}
