<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;
use PHPUnit\Framework\TestCase;

class FineUploaderResponseTest extends TestCase
{
    public function testCreationOfResponse(): void
    {
        $response = new FineUploaderResponse();

        $this->assertTrue($response->getSuccess());
        $this->assertNull($response->getError());
    }

    public function testFillOfResponse(): void
    {
        $response = new FineUploaderResponse();

        $cat = 'is grumpy';
        $dog = 'has no idea';
        $del = 'nothing here';

        $response['cat'] = $cat;
        $response['dog'] = $dog;
        $response['del'] = $del;
        $response->setSuccess(false);

        // the next three lines are from code coverage
        $this->assertTrue(isset($response['cat']));
        $this->assertSame($response['cat'], $cat);

        unset($response['del']);

        $assembled = $response->assemble();

        $this->assertIsArray($assembled);
        $this->assertArrayHasKey('cat', $assembled);
        $this->assertArrayHasKey('dog', $assembled);
        $this->assertSame($assembled['cat'], $cat);
        $this->assertSame($assembled['dog'], $dog);
        $this->assertFalse($response->getSuccess());
        $this->assertNull($response->getError());
    }

    public function testError(): void
    {
        $response = new FineUploaderResponse();
        $response->setError('This response is grumpy');

        $this->assertSame($response->getError(), 'This response is grumpy');
    }

    public function testOverwriteOfInternals(): void
    {
        $response = new FineUploaderResponse();
        $response['success'] = false;
        $response['error'] = 42;

        $this->assertTrue($response->getSuccess());
        $this->assertNull($response->getError());

        $assembled = $response->assemble();

        $this->assertTrue($assembled['success']);
        $this->assertArrayNotHasKey('error', $assembled);
    }
}
