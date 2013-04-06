<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Oneup\UploaderBundle\Uploader\Naming\UniqidNamer;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Controller\UploaderController;

class ControllerUploadTest extends \PHPUnit_Framework_TestCase
{
    protected $tempFile;
    
    public function setUp()
    {
        // create temporary file
        $this->tempFile = tempnam(sys_get_temp_dir(), 'uploader');
        
        $pointer = fopen($this->tempFile, 'w+');
        fwrite($pointer, str_repeat('A', 1024), 1024);
        fclose($pointer);
    }
    
    public function testUpload()
    {
        $container = $this->getContainerMock();
        $storage = new FilesystemStorage(sys_get_temp_dir() . '/uploader');
        $config = array(
            'use_orphanage' => false,
            'namer' => 'namer',
            'max_size' => 2048,
            'allowed_extensions' => array(),
            'disallowed_extensions' => array()
        );
        
        $controller = new UploaderController($container, $storage, $config, 'cat');
        $response = $controller->upload();
        
        // check if original file has been moved
        $this->assertFalse(file_exists($this->tempFile));
        
        // testing response
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        // check if file is present
        $finder = new Finder();
        $finder->in(sys_get_temp_dir() . '/uploader')->files();
        
        $this->assertCount(1, $finder);
    }
    
    protected function getContainerMock()
    {
        $mock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $mock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'containerGetCb')))
        ;
        
        return $mock;
    }
    
    public function containerGetCb($inp)
    {
        if($inp == 'request')
            return $this->getRequestMock();
        
        if($inp == 'event_dispatcher')
            return $this->getEventDispatcherMock();
        
        if($inp == 'namer')
            return new UniqidNamer();
    }
    
    protected function getEventDispatcherMock()
    {
        $mock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $mock
            ->expects($this->any())
            ->method('dispatch')
            ->will($this->returnValue(true))
        ;
        
        return $mock;
    }
    
    protected function getRequestMock()
    {
        $mock = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mock
            ->expects($this->any())
            ->method('get')
            ->with('qqtotalparts')
            ->will($this->returnValue(1))
        ;
        
        $mock->files = array(
            $this->getUploadedFile()
        );
        
        return $mock;
    }
    
    protected function getUploadedFile()
    {
        return new UploadedFile($this->tempFile, 'grumpy-cat.jpeg', 'image/jpeg', 1024, null, true);
    }
    
    public function tearDown()
    {
        // remove all files in tmp folder
        $filesystem = new Filesystem();
        $filesystem->remove(sys_get_temp_dir() . '/uploader');
    }
}