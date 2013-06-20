<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;

class TestFineUploaderResponse extends \PHPUnit_Framework_TestCase
{
    public function testCreationOfResponse()
    {
        $response = new FineUploaderResponse();

        $this->assertTrue($response->getSuccess());
        $this->assertNull($response->getError());
    }

    public function testFillOfResponse()
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
        $this->assertEquals($response['cat'], $cat);

        unset($response['del']);

        $assembled = $response->assemble();

        $this->assertTrue(is_array($assembled));
        $this->assertArrayHasKey('cat', $assembled);
        $this->assertArrayHasKey('dog', $assembled);
        $this->assertEquals($assembled['cat'], $cat);
        $this->assertEquals($assembled['dog'], $dog);
        $this->assertFalse($response->getSuccess());
        $this->assertNull($response->getError());
    }

    public function testError()
    {
        $response = new FineUploaderResponse();
        $response->setError('This response is grumpy');

        $this->assertEquals($response->getError(), 'This response is grumpy');
    }

    public function testOverwriteOfInternals()
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
