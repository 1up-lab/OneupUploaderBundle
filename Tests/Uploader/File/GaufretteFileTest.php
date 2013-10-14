<?php
namespace Oneup\UploaderBundle\Tests\Uploader\File;

use Gaufrette\File;
use Gaufrette\StreamWrapper;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Gaufrette\Adapter\Local as Adapter;
use Gaufrette\Filesystem as GaufretteFileSystem;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;

class GaufretteFileTest extends FileTest
{
    public function setUp()
    {
        $adapter = new Adapter(sys_get_temp_dir(), true);
        $filesystem = new GaufretteFilesystem($adapter);

        $map = StreamWrapper::getFilesystemMap();
        $map->set('oneup', $filesystem);

        StreamWrapper::register();

        $this->storage = new GaufretteStorage($filesystem, 100000);

        $this->path = 'oneup_test_tmp';
        mkdir(sys_get_temp_dir().'/'.$this->path);

        $this->basename = 'test_file.txt';
        $this->pathname = $this->path .'/'. $this->basename;
        $this->extension = 'txt';
        $this->size = 9; //something = 9 bytes
        $this->mimeType = 'text/plain';

        file_put_contents(sys_get_temp_dir() .'/' . $this->pathname, 'something');

        $this->file = new GaufretteFile(new File($this->pathname, $filesystem), $filesystem, 'gaufrette://oneup/');
    }

    public function tearDown()
    {
        unlink(sys_get_temp_dir().'/'.$this->pathname);
        rmdir(sys_get_temp_dir().'/'.$this->path);
    }
}
