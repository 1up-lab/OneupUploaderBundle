<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractUploadTest;

class YUI3Test extends AbstractUploadTest
{
    protected function getConfigKey()
    {
        return 'yui3';
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
