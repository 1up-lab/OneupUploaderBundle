<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FileBagExtractorText extends \PHPUnit_Framework_TestCase
{
    protected $method;
    protected $mock;

    public function setUp()
    {
        $controller = 'Oneup\UploaderBundle\Controller\AbstractController';
        $mock = $this->getMockBuilder($controller)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $method = new \ReflectionMethod($controller, 'getFiles');
        $method->setAccessible(true);

        $this->method = $method;
        $this->mock = $mock;
    }

    public function testEmpty()
    {
        $result = $this->invoke(new FileBag());

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testWithNullArrayValue()
    {
        $bag = new FileBag(array(
            array(null)
        ));

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testWithSingleFile()
    {
        $bag = new FileBag(array(
            new UploadedFile(__FILE__, 'name')
        ));

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result);
    }

    public function testWithMultipleFiles()
    {
        $bag = new FileBag(array(
            new UploadedFile(__FILE__, 'name1'),
            new UploadedFile(__FILE__, 'name2'),
            new UploadedFile(__FILE__, 'name3')
        ));

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    public function testWithMultipleFilesContainingNullValues()
    {
        $bag = new FileBag(array(
            // we need to inject an array,
            // otherwise the FileBag will type check against
            // UploadedFile resulting in an InvalidArgumentException.
            array(
                new UploadedFile(__FILE__, 'name1'),
                null,
                new UploadedFile(__FILE__, 'name2'),
                null,
                new UploadedFile(__FILE__, 'name3')
            )
        ));

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    protected function invoke(FileBag $bag)
    {
        return $this->method->invoke($this->mock, $bag);
    }
}
