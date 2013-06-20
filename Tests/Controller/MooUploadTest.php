<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

class MooUploadTest extends AbstractControllerTest
{
    protected function getConfigKey()
    {
        return 'mooupload';
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
