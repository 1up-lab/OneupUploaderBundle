<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\Naming\GaufretteNamer;

class GaufretteNamerTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $file = $this->getMockBuilder('Oneup\UploaderBundle\Uploader\File\FilesystemFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $file
            ->expects($this->any())
            ->method('getExtension')
            ->will($this->returnValue('jpeg'))
        ;

        $filesystem = $this->getMockBuilder('Gaufrette\Filesystem')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $filesystem
            ->expects($this->at(0))
            ->method('has')
            ->will($this->returnValue(true))
        ;

        $filesystem
            ->expects($this->at(1))
            ->method('has')
            ->will($this->returnValue(false))
        ;

        $namer = $this->getMock('Oneup\UploaderBundle\Uploader\Naming\NamerInterface');

        $namer
            ->expects($this->at(0))
            ->method('name')
            ->will($this->returnValue('foo'))
        ;

        $namer
            ->expects($this->at(1))
            ->method('name')
            ->will($this->returnValue('bar'))
        ;

        $gaufretteNamer = new GaufretteNamer($filesystem, $namer);
        $this->assertEquals('bar', $gaufretteNamer->name($file));
    }
}
