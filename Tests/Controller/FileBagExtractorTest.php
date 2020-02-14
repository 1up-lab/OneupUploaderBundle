<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FileBagExtractorTest extends TestCase
{
    protected $method;
    protected $mock;

    public function setUp(): void
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

    public function testEmpty(): void
    {
        $result = $this->invoke(new FileBag());

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testWithNullArrayValue(): void
    {
        $bag = new FileBag([
            [null],
        ]);

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testWithSingleFile(): void
    {
        $bag = new FileBag([
            new UploadedFile(__FILE__, 'name'),
        ]);

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result);
    }

    public function testWithMultipleFiles(): void
    {
        $bag = new FileBag([
            new UploadedFile(__FILE__, 'name1'),
            new UploadedFile(__FILE__, 'name2'),
            new UploadedFile(__FILE__, 'name3'),
        ]);

        $result = $this->invoke($bag);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    public function testWithMultipleFilesContainingNullValues(): void
    {
        $bag = new FileBag([
            // we need to inject an array,
            // otherwise the FileBag will type check against
            // UploadedFile resulting in an InvalidArgumentException.
            [
                new UploadedFile(__FILE__, 'name1'),
                null,
                new UploadedFile(__FILE__, 'name2'),
                null,
                new UploadedFile(__FILE__, 'name3'),
            ],
        ]);

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
