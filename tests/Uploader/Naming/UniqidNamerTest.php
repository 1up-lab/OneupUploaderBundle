<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\Naming\UniqidNamer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UniqidNamerTest extends TestCase
{
    public function testNamerReturnsName(): void
    {
        $file = $this->createMock(FilesystemFile::class);

        $file
            ->method('getExtension')
            ->willReturn('jpeg')
        ;

        $namer = new UniqidNamer();
        $this->assertMatchesRegularExpression('/[a-z0-9]{13}.jpeg/', $namer->name($file));
    }

    public function testNamerReturnsUniqueName(): void
    {
        /** @var FilesystemFile&MockObject $file */
        $file = $this->createMock(FilesystemFile::class);

        $file
            ->method('getExtension')
            ->willReturn('jpeg')
        ;

        $namer = new UniqidNamer();

        // get two different names
        $name1 = $namer->name($file);
        $name2 = $namer->name($file);

        $this->assertNotSame($name1, $name2);
    }
}
