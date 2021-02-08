<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Controller\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FileBagExtractorTest extends TestCase
{
    /**
     * @var \ReflectionMethod
     */
    protected $method;

    /**
     * @var MockObject
     */
    protected $mock;

    protected function setUp(): void
    {
        $controller = AbstractController::class;
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

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testWithNullArrayValue(): void
    {
        $bag = new FileBag([]);

        $result = $this->invoke($bag);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testWithSingleFile(): void
    {
        $bag = new FileBag([
            new UploadedFile(__FILE__, 'name'),
        ]);

        $result = $this->invoke($bag);

        $this->assertIsArray($result);
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

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    /**
     * @return mixed
     */
    protected function invoke(FileBag $bag)
    {
        return $this->method->invoke($this->mock, $bag);
    }
}
