<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractChunkedUploadTest;

class PluploadTest extends AbstractChunkedUploadTest
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

    protected function getNextRequestParameters($i)
    {
        return array(
            'chunks' => $this->total,
            'chunk' => $i,
            'name' => 'cat.txt'
        );
    }

    protected function getNextFile($i)
    {
        return new UploadedFile(
            $this->createTempFile(20),
            'cat.txt',
            'text/plain',
            20
        );
    }
}
