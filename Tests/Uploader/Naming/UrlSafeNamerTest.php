<?php
namespace Oneup\UploaderBundle\Tests\Uploader\Naming;

use Oneup\UploaderBundle\Tests\Uploader\File\FileTest;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Naming\UrlSafeNamer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UrlSafeNamerTest extends FileTest
{

    public function setUp()
    {
        $this->path = sys_get_temp_dir(). '/oneup_namer_test';
        mkdir($this->path);

        $this->basename = 'test_file.txt';
        $this->pathname = $this->path .'/'. $this->basename;
        $this->extension = 'txt';
        $this->size = 9; //something = 9 bytes
        $this->mimeType = 'text/plain';

        file_put_contents($this->pathname, 'something');

        $this->file = new FilesystemFile(new UploadedFile($this->pathname, 'test_file.txt', null, null, null, true));
    }

    public function testCanGetString()
    {
        $namer = new UrlSafeNamer();
        $this->assertTrue(is_string($namer->name($this->file)));
        $this->assertStringEndsWith($this->extension, $namer->name($this->file));
    }

    public function test_two_file_names_not_equal()
    {
        $namer = new UrlSafeNamer();
        // Trying 200 times just to be sure
        for($i = 0; $i < 200; $i++) {
            $name1 = $namer->name($this->file);
            $name2 = $namer->name($this->file);
            $this->assertNotEquals($name1, $name2);
        }

    }

    public function tearDown()
    {
        unlink($this->pathname);
        rmdir($this->path);
    }

}
