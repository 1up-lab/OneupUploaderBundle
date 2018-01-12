<?php

namespace Oneup\UploaderBundle\Tests\Controller;

class MooUploadTest extends AbstractControllerTest
{
    protected function getConfigKey()
    {
        return 'mooupload';
    }

    protected function getRequestParameters()
    {
        return [];
    }

    protected function getRequestFile()
    {
        return [new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain',
            128
        )];
    }
}
