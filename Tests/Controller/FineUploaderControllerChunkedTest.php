<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Oneup\UploaderBundle\Uploader\Chunk\ChunkManager;
use Oneup\UploaderBundle\Uploader\Naming\UniqidNamer;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Controller\FineUploaderController;

class FineUploaderControllerChunkedTest extends \PHPUnit_Framework_TestCase
{
    protected $tempChunks;
    protected $currentChunk;
    protected $chunkUuid;
    protected $numberOfChunks;
    
    public function setUp()
    {
        $this->numberOfChunks = 10;
        
        // create 10 chunks
        for($i = 0; $i < $this->numberOfChunks; $i++)
        {
            // create temporary file
            $chunk = tempnam(sys_get_temp_dir(), 'uploader');
        
            $pointer = fopen($chunk, 'w+');
            fwrite($pointer, str_repeat('A', 1024), 1024);
            fclose($pointer);
            
            $this->tempChunks[] = $chunk;
        }
        
        $this->currentChunk = 0;
        $this->chunkUuid = uniqid();
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

        $responses = array();
        $controller = new FineUploaderController($container, $storage, $config, 'cat');
        
        // mock as much requests as there are parts to assemble
        for($i = 0; $i < $this->numberOfChunks; $i ++)
        {
            $responses[] = $controller->upload();
            
            // will be used internaly
            $this->currentChunk++;
        }
        
        for($i = 0; $i < $this->numberOfChunks; $i ++)
        {
            // check if original file has been moved
            $this->assertFalse(file_exists($this->tempChunks[$i]));
            
            $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $responses[$i]);
            $this->assertEquals(200, $responses[$i]->getStatusCode());
        }
        
        // check if assembled file is here
        $finder = new Finder();
        $finder->in(sys_get_temp_dir() . '/uploader')->files();
        $this->assertCount(1, $finder);
        
        // and check if chunks are gone
        $finder = new Finder();
        $finder->in(sys_get_temp_dir() . '/chunks')->files();
        $this->assertCount(0, $finder);
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
        
        if($inp == 'oneup_uploader.chunk_manager')
            return $this->getChunkManager();
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
            ->will($this->returnCallback(array($this, 'requestGetCb')))
        ;
        
        $mock->files = array(
            $this->getUploadedFile()
        );
        
        return $mock;
    }
    
    public function requestGetCb($inp)
    {
        if($inp == 'qqtotalparts')
            return $this->numberOfChunks;
        
        if($inp == 'qqpartindex')
            return $this->currentChunk;
        
        if($inp == 'qquuid')
            return $this->chunkUuid;
        
        if($inp == 'qqfilename')
            return 'grumpy-cat.jpeg';
    }
    
    protected function getUploadedFile()
    {
        return new UploadedFile($this->tempChunks[$this->currentChunk], 'grumpy-cat.jpeg', 'image/jpeg', 1024, null, true);
    }
    
    protected function getChunkManager()
    {
        return new ChunkManager(array(
            'directory' => sys_get_temp_dir() . '/chunks'
        ));
    }
    
    public function tearDown()
    {
        // remove all files in tmp folder
        $filesystem = new Filesystem();
        $filesystem->remove(sys_get_temp_dir() . '/uploader');
        $filesystem->remove(sys_get_temp_dir() . '/chunks');
    }
}