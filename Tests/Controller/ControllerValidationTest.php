<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Controller\UploaderController;

class ControllerValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Symfony\Component\HttpFoundation\File\Exception\UploadException
     */
    public function testMaxSizeValidationFails()
    {
        // create a config
        $config = array();
        $config['max_size'] = 10;
        $config['allowed_extensions'] = array();
        $config['disallowed_extensions'] = array();
        
        $this->performConfigTest($config);
    }
    
    public function testMaxSizeValidationPasses()
    {
        // create a config
        $config = array();
        $config['max_size'] = 20;
        $config['allowed_extensions'] = array();
        $config['disallowed_extensions'] = array();
        
        $this->performConfigTest($config);
    }

    /**
     * @expectedException Symfony\Component\HttpFoundation\File\Exception\UploadException
     */
    public function testAllowedExtensionValidationFails()
    {
        // create a config
        $config = array();
        $config['max_size'] = 20;
        $config['allowed_extensions'] = array('txt', 'pdf');
        $config['disallowed_extensions'] = array();
        
        $this->performConfigTest($config);
    }
    
    public function testAllowedExtensionValidationPasses()
    {
        // create a config
        $config = array();
        $config['max_size'] = 20;
        $config['allowed_extensions'] = array('png', 'jpg', 'jpeg', 'gif');
        $config['disallowed_extensions'] = array();
        
        $this->performConfigTest($config);
    }

    /**
     * @expectedException Symfony\Component\HttpFoundation\File\Exception\UploadException
     */
    public function testDisallowedExtensionValidationFails()
    {
        // create a config
        $config = array();
        $config['max_size'] = 20;
        $config['allowed_extensions'] = array();
        $config['disallowed_extensions'] = array('jpeg');
        
        $this->performConfigTest($config);
    }
    
    public function testDisallowedExtensionValidationPasses()
    {
        // create a config
        $config = array();
        $config['max_size'] = 20;
        $config['allowed_extensions'] = array();
        $config['disallowed_extensions'] = array('exe', 'bat');
        
        $this->performConfigTest($config);
    }
    
    protected function performConfigTest($config)
    {
        // prepare mock
        $file = $this->getUploadedFileMock();
        $method = $this->getValidationMethod();
        
        $container = $this->getContainerMock();
        $storage = $this->getStorageMock();
        
        $controller = new UploaderController($container, $storage, $config, 'cat');
        $method->invoke($controller, $file);
        
        // yey, no exception thrown
        $this->assertTrue(true);
    }
    
    protected function getUploadedFileMock()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $file
            ->expects($this->any())
            ->method('getClientSize')
            ->will($this->returnValue(15))
        ;
        
        $file
            ->expects($this->any())
            ->method('getClientOriginalName')
            ->will($this->returnValue('grumpycat.jpeg'))
        ;
        
        return $file;
    }
    
    protected function getContainerMock()
    {
        return $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
    }
    
    protected function getStorageMock()
    {
        return $this->getMock('Oneup\UploaderBundle\Uploader\Storage\StorageInterface');
    }
    
    protected function getValidationMethod()
    {
        // create a public version of the validate method
        $class = new \ReflectionClass('Oneup\\UploaderBundle\\Controller\\UploaderController');
        $method = $class->getMethod('validate');
        $method->setAccessible(true);
        
        return $method;
    }
}