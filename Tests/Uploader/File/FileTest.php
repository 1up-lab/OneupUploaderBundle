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
        $this->assertSame($this->pathname, $this->file->getPathname());
    }

    public function testGetPath()
    {
        $this->assertSame($this->path, $this->file->getPath());
    }

    public function testGetBasename()
    {
        $this->assertSame($this->basename, $this->file->getBasename());
    }

    public function testGetExtension()
    {
        $this->assertSame($this->extension, $this->file->getExtension());
    }

    public function testGetSize()
    {
        $this->assertSame($this->size, $this->file->getSize());
    }

    public function testGetMimeType()
    {
        $this->assertSame($this->mimeType, $this->file->getMimeType());
    }
}
