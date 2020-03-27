<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FineUploaderTest extends AbstractChunkedUploadTest
{
    protected function getConfigKey(): string
    {
        return 'fineuploader';
    }

    protected function getRequestParameters(): array
    {
        return [];
    }

    protected function getRequestFile(): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(128),
            'cat.txt',
            'text/plain'
        );
    }

    protected function getNextRequestParameters(int $i): array
    {
        return [
            'qqtotalparts' => $this->total,
            'qqpartindex' => $i,
            'qquuid' => 'veryuuid',
            'qqfilename' => 'cat.txt',
        ];
    }

    protected function getNextFile(int $i): UploadedFile
    {
        return new UploadedFile(
            $this->createTempFile(20),
            'cat.txt',
            'text/plain'
        );
    }
}
