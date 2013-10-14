<?php

namespace Oneup\UploaderBundle\Tests\DependencyInjection;

class OneupUploaderExtensionTest extends \PHPUnit_Framework_TestCase
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

        $this->assertEquals(15, $method->invoke($mock, ' 15'));
        $this->assertEquals(15, $method->invoke($mock, '15 '));

        $this->assertEquals(1024, $method->invoke($mock, '1K'));
        $this->assertEquals(2048, $method->invoke($mock, '2K'));
        $this->assertEquals(1048576, $method->invoke($mock, '1M'));
        $this->assertEquals(2097152, $method->invoke($mock, '2M'));
        $this->assertEquals(1073741824, $method->invoke($mock, '1G'));
        $this->assertEquals(2147483648, $method->invoke($mock, '2G'));
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

        $this->assertEquals('gaufrette://gallery/', $output1);
        $this->assertEquals('gaufrette://gallery/', $output2);
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

        $store = array(
            $getValueInBytes->invoke($mock, ini_get('upload_max_filesize')),
            $getValueInBytes->invoke($mock, ini_get('post_max_size'))
        );

        $min = min($store);

        $this->assertEquals(0, $getMaxUploadSize->invoke($mock, 0));
        $this->assertEquals(min(10, $min), $getMaxUploadSize->invoke($mock, min(10, $min)));
        $this->assertEquals(min(\PHP_INT_MAX, $min), $getMaxUploadSize->invoke($mock, min(\PHP_INT_MAX, $min)));
    }
}
