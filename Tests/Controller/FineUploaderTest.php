<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Tests\Controller\AbstractChunkedUploadTest;

class FineUploaderTest extends AbstractChunkedUploadTest
{
    protected function getConfigKey()
    {
        return 'fineuploader';
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
            'qqtotalparts' => $this->total,
            'qqpartindex' => $i,
            'qquuid' => 'veryuuid',
            'qqfilename' => 'cat.txt'
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
