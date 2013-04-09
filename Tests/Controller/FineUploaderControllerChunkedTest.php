<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerChunkedTest;

class FineUploaderControllerChunkedTest extends AbstractControllerChunkedTest
{
    public function getControllerString()
    {
        return 'Oneup\UploaderBundle\Controller\FineUploaderController';
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
}