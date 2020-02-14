<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class UploadifyValidationTest extends AbstractValidationTest
{
    protected function getConfigKey()
    {
        return 'uploadify_validation';
    }

    protected function getRequestParameters()
    {
        return [];
    }

    protected function getOversizedFile()
    {
        // TODO at EOL of SF 3.4 this can be removed
        if(Kernel::VERSION_ID < 40400) {
            return new UploadedFile(
                $this->createTempFile(512),
                'cat.ok',
                'text/plain',
                512
            );
        }

        return new UploadedFile(
            $this->createTempFile(512),
            'cat.ok',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeType()
    {
        // TODO at EOL of SF 3.4 this can be removed
        if(Kernel::VERSION_ID < 40400) {
            return new UploadedFile(
                $this->createTempFile(128),
                'cat.txt',
                'text/plain',
                128
            );
        }

        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        );
    }

    protected function getFileWithCorrectMimeTypeAndIncorrectExtension()
    {
        // TODO at EOL of SF 3.4 this can be removed
        if(Kernel::VERSION_ID < 40400) {
            return new UploadedFile(
                $this->createTempFile(128),
                'cat.txxt',
                'text/plain',
                128
            );
        }

        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txxt',
            'text/plain'
        );
    }

    protected function getFileWithIncorrectMimeType()
    {
        // TODO at EOL of SF 3.4 this can be removed
        if(Kernel::VERSION_ID < 40400) {
            return new UploadedFile(
                $this->createTempFile(128),
                'cat.ok',
                'image/gif',
                128
            );
        }

        return new UploadedFile(
            $this->createTempFile(128),
            'cat.ok',
            'image/gif'
        );
    }
}
