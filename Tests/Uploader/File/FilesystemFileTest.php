<?php
namespace Oneup\UploaderBundle\Tests\Uploader\File;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemFileTest extends FileTest
{
    public function setUp()
    {
        $this->path = sys_get_temp_dir(). '/oneup_test_tmp';
        mkdir($this->path);

        $this->basename = 'test_file.txt';
        $this->pathname = $this->path .'/'. $this->basename;
        $this->extension = 'txt';
        $this->size = 9; //something = 9 bytes
        $this->mimeType = 'text/plain';

        file_put_contents($this->pathname, 'something');

        $this->file = new FilesystemFile(new UploadedFile($this->pathname, 'test_file.txt', null, null, null, true));
    }

    public function tearDown()
    {
        unlink($this->pathname);
        rmdir($this->path);
    }
}
