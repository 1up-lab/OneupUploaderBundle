<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

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
        // TODO at EOL of SF 3.4 this can be removed
        if(Kernel::VERSION_ID < 40400) {
            return [new UploadedFile(
                $this->createTempFile(128),
                'cat.txt',
                'text/plain',
                128
            )];
        }

        return [new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        )];
    }
}
