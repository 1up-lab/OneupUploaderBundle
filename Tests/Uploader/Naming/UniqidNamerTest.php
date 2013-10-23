<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\Naming\UniqidNamer;

class UniqidNamerTest extends \PHPUnit_Framework_TestCase
{
    public function testNamerReturnsName()
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

        $namer = new UniqidNamer();
        $this->assertRegExp('/[a-z0-9]{13}.jpeg/', $namer->name($file));
    }

    public function testNamerReturnsUniqueName()
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

        $namer = new UniqidNamer();

        // get two different names
        $name1 = $namer->name($file);
        $name2 = $namer->name($file);

        $this->assertNotEquals($name1, $name2);
    }
}
