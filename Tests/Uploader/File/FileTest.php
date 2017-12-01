<?php
namespace Oneup\UploaderBundle\Tests\Uploader\File;

use PHPUnit\Framework\TestCase;

abstract class FileTest extends TestCase
{
    protected $file;
    protected $pathname;
    protected $path;
    protected $basename;
    protected $extension;
    protected $size;
    protected $mimeType;

    public function testGetPathName()
    {
        $this->assertEquals($this->pathname, $this->file->getPathname());
    }

    public function testGetPath()
    {
        $this->assertEquals($this->path, $this->file->getPath());
    }

    public function testGetBasename()
    {
        $this->assertEquals($this->basename, $this->file->getBasename());
    }

    public function testGetExtension()
    {
        $this->assertEquals($this->extension, $this->file->getExtension());
    }

    public function testGetSize()
    {
        $this->assertEquals($this->size, $this->file->getSize());
    }

    public function testGetMimeType()
    {
        $this->assertEquals($this->mimeType, $this->file->getMimeType());
    }
}
