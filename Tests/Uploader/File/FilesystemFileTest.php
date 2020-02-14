<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\File;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;

class FilesystemFileTest extends FileTest
{
    public function setUp(): void
    {
        $this->path = sys_get_temp_dir() . '/oneup_test_tmp';

        if (!file_exists($this->path)) {
            mkdir($this->path);
        }

        $this->basename = 'test_file.txt';
        $this->pathname = $this->path . '/' . $this->basename;
        $this->extension = 'txt';
        $this->size = 9; //something = 9 bytes
        $this->mimeType = 'text/plain';

        file_put_contents($this->pathname, 'something');

        $file = new UploadedFile($this->pathname, 'test_file.txt', null, null, true);
        $this->file = new FilesystemFile($file);
    }

    public function tearDown(): void
    {
        unlink($this->pathname);
        rmdir($this->path);
    }
}
