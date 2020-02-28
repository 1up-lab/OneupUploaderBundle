<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\File;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

abstract class FileTest extends TestCase
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $pathname;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $basename;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $mimeType;

    public function testGetPathName(): void
    {
        $this->assertSame($this->pathname, $this->file->getPathname());
    }

    public function testGetPath(): void
    {
        $this->assertSame($this->path, $this->file->getPath());
    }

    public function testGetBasename(): void
    {
        $this->assertSame($this->basename, $this->file->getBasename());
    }

    public function testGetExtension(): void
    {
        $this->assertSame($this->extension, $this->file->getExtension());
    }

    public function testGetSize(): void
    {
        $this->assertSame($this->size, $this->file->getSize());
    }

    public function testGetMimeType(): void
    {
        $this->assertSame($this->mimeType, $this->file->getMimeType());
    }
}
