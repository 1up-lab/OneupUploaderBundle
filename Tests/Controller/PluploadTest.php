<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class PluploadTest extends AbstractChunkedUploadTest
{
    protected function getConfigKey()
    {
        return 'plupload';
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
            'text/plain'
        );
    }

    protected function getNextRequestParameters($i)
    {
        return [
            'chunks' => $this->total,
            'chunk' => $i,
            'name' => 'cat.txt',
        ];
    }

    protected function getNextFile($i)
    {
        return new UploadedFile(
            $this->createTempFile(20),
            'cat.txt',
            'text/plain'
        );
    }
}
