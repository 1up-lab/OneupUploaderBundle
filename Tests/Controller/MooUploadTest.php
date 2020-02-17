<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MooUploadTest extends AbstractControllerTest
{
    protected function getConfigKey(): string
    {
        return 'mooupload';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getRequestFile(): array
    {
        return [new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        )];
    }
}
