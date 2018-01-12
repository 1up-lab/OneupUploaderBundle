<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadifyTest extends AbstractUploadTest
{
    protected function getConfigKey()
    {
        return 'uploadify';
    }

    protected function getRequestParameters()
    {
        return [];
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
