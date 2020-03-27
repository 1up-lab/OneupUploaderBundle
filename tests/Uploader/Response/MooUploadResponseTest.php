<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\MooUploadResponse;
use PHPUnit\Framework\TestCase;

class MooUploadResponseTest extends TestCase
{
    public function testCreationOfResponse(): void
    {
        $response = new MooUploadResponse();

        $this->assertTrue($response->getFinish());
        $this->assertSame(0, $response->getError());
    }

    public function testFunctionsOfResponse(): void
    {
        $response = new MooUploadResponse();
        $response->setId(3);
        $response->setName('grumpy_cat.jpg');
        $response->setSize(15093);
        $response->setError(-1);
        $response->setFinish(true);
        $response->setUploadedName('b1/2d/b12d23.jpg');

        $this->assertSame(3, $response->getId());
        $this->assertSame('grumpy_cat.jpg', $response->getName());
        $this->assertSame(15093, $response->getSize());
        $this->assertSame(-1, $response->getError());
        $this->assertTrue($response->getFinish());
        $this->assertSame('b1/2d/b12d23.jpg', $response->getUploadedName());
    }

    public function testFunctionsAfterOverwrite(): void
    {
        $response = new MooUploadResponse();
        $response->setId(3);
        $response->setName('grumpy_cat.jpg');
        $response->setSize(15093);
        $response->setError(-1);
        $response->setFinish(true);
        $response->setUploadedName('b1/2d/b12d23.jpg');

        $response['id'] = null;
        $response['name'] = null;
        $response['size'] = null;
        $response['error'] = null;
        $response['finish'] = null;
        $response['uploadedName'] = null;
        $response['princess'] = true;

        $this->assertSame(3, $response->getId());
        $this->assertSame('grumpy_cat.jpg', $response->getName());
        $this->assertSame(15093, $response->getSize());
        $this->assertSame(-1, $response->getError());
        $this->assertTrue($response->getFinish());
        $this->assertSame('b1/2d/b12d23.jpg', $response->getUploadedName());
        $this->assertTrue($response['princess']);
    }

    public function testAssemble(): void
    {
        $response = new MooUploadResponse();
        $response->setId(3);
        $response->setName('grumpy_cat.jpg');
        $response->setSize(15093);
        $response->setError(-1);
        $response->setFinish(true);
        $response->setUploadedName('b1/2d/b12d23.jpg');

        $response['id'] = null;
        $response['name'] = null;
        $response['size'] = null;
        $response['error'] = null;
        $response['finish'] = null;
        $response['uploadedName'] = null;
        $response['upload_name'] = null;
        $response['princess'] = true;

        $data = $response->assemble();

        $this->assertSame(3, $data['id']);
        $this->assertSame('grumpy_cat.jpg', $data['name']);
        $this->assertSame(15093, $data['size']);
        $this->assertSame(-1, $data['error']);
        $this->assertTrue($data['finish']);
        $this->assertSame('b1/2d/b12d23.jpg', $data['upload_name']);
        $this->assertTrue($data['princess']);
    }
}
