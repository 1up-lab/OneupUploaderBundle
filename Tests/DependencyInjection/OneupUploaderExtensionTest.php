<?php

namespace Oneup\UploaderBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;

class OneupUploaderExtensionTest extends TestCase
{
    public function testValueToByteTransformer()
    {
        $mock = $this->getMockBuilder('Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $method = new \ReflectionMethod(
            'Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension',
            'getValueInBytes'
        );
        $method->setAccessible(true);

        $this->assertSame(15, $method->invoke($mock, ' 15'));
        $this->assertSame(15, $method->invoke($mock, '15 '));

        $this->assertSame(1024, $method->invoke($mock, '1K'));
        $this->assertSame(2048, $method->invoke($mock, '2K'));
        $this->assertSame(1048576, $method->invoke($mock, '1M'));
        $this->assertSame(2097152, $method->invoke($mock, '2M'));
        $this->assertSame(1073741824, $method->invoke($mock, '1G'));
        $this->assertSame(2147483648, $method->invoke($mock, '2G'));
    }

    public function testNormalizationOfStreamWrapper()
    {
        $mock = $this->getMockBuilder('Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $method = new \ReflectionMethod(
            'Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension',
            'normalizeStreamWrapper'
        );
        $method->setAccessible(true);

        $output1 = $method->invoke($mock, 'gaufrette://gallery');
        $output2 = $method->invoke($mock, 'gaufrette://gallery/');
        $output3 = $method->invoke($mock, null);

        $this->assertSame('gaufrette://gallery/', $output1);
        $this->assertSame('gaufrette://gallery/', $output2);
        $this->assertNull($output3);
    }

    public function testGetMaxUploadSize()
    {
        $mock = $this->getMockBuilder('Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $getMaxUploadSize = new \ReflectionMethod(
            'Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension',
            'getMaxUploadSize'
        );

        $getValueInBytes = new \ReflectionMethod(
            'Oneup\UploaderBundle\DependencyInjection\OneupUploaderExtension',
            'getValueInBytes'
        );

        $getMaxUploadSize->setAccessible(true);
        $getValueInBytes->setAccessible(true);

        $store = [
            $getValueInBytes->invoke($mock, ini_get('upload_max_filesize')),
            $getValueInBytes->invoke($mock, ini_get('post_max_size')),
        ];

        $min = min($store);

        $this->assertSame(0, $getMaxUploadSize->invoke($mock, 0));
        $this->assertSame(min(10, $min), $getMaxUploadSize->invoke($mock, min(10, $min)));
        $this->assertSame(min(\PHP_INT_MAX, $min), $getMaxUploadSize->invoke($mock, min(\PHP_INT_MAX, $min)));
    }
}
