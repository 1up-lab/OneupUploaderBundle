<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use PHPUnit\Framework\TestCase;

class EmptyResponseTest extends TestCase
{
    public function testEmpty(): void
    {
        $response = new EmptyResponse();
        $assembled = $response->assemble();

        $this->assertIsArray($assembled);
        $this->assertCount(0, $assembled);
    }

    public function testWithItems(): void
    {
        $response = new EmptyResponse();

        // fill in some data
        $response['cat'] = 'grumpy';
        $response['dog'] = 'has no idea';

        $assembled = $response->assemble();

        $this->assertIsArray($assembled);
        $this->assertCount(2, $assembled);
    }
}
