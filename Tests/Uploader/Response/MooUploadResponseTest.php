<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\MooUploadResponse;

class MooUploadResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationOfResponse()
    {
        $response = new MooUploadResponse();

        $this->assertTrue($response->getFinish());
        $this->assertEquals(0, $response->getError());
    }

    public function testFunctionsOfResponse()
    {
        $response = new MooUploadResponse();
        $response->setId(3);
        $response->setName('grumpy_cat.jpg');
        $response->setSize(15093);
        $response->setError(-1);
        $response->setFinish(true);
        $response->setUploadedName('b1/2d/b12d23.jpg');

        $this->assertEquals(3, $response->getId());
        $this->assertEquals('grumpy_cat.jpg', $response->getName());
        $this->assertEquals(15093, $response->getSize());
        $this->assertEquals(-1, $response->getError());
        $this->assertEquals(true, $response->getFinish());
        $this->assertEquals('b1/2d/b12d23.jpg', $response->getUploadedName());
    }

    public function testFunctionsAfterOverwrite()
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
        $response['princess'] = !null;

        $this->assertEquals(3, $response->getId());
        $this->assertEquals('grumpy_cat.jpg', $response->getName());
        $this->assertEquals(15093, $response->getSize());
        $this->assertEquals(-1, $response->getError());
        $this->assertEquals(true, $response->getFinish());
        $this->assertEquals('b1/2d/b12d23.jpg', $response->getUploadedName());
        $this->assertEquals(!null, $response['princess']);
    }

    public function testAssemble()
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
        $response['princess'] = !null;

        $data = $response->assemble();

        $this->assertEquals(3, $data['id']);
        $this->assertEquals('grumpy_cat.jpg', $data['name']);
        $this->assertEquals(15093, $data['size']);
        $this->assertEquals(-1, $data['error']);
        $this->assertEquals(true, $data['finish']);
        $this->assertEquals('b1/2d/b12d23.jpg', $data['upload_name']);
        $this->assertEquals(!null, $data['princess']);
    }
}
