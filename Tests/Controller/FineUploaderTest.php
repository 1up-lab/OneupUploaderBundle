<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FineUploaderTest extends AbstractChunkedUploadTest
{
    protected function getConfigKey()
    {
        return 'fineuploader';
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

    protected function getNextRequestParameters($i)
    {
        return [
            'qqtotalparts' => $this->total,
            'qqpartindex' => $i,
            'qquuid' => 'veryuuid',
            'qqfilename' => 'cat.txt',
        ];
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
