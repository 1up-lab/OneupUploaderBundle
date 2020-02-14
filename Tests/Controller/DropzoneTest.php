<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class DropzoneTest extends AbstractUploadTest
{
    protected function getConfigKey()
    {
        return 'dropzone';
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
}
