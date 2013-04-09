<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerChunkedTest;

class BlueimpControllerChunkedTest extends AbstractControllerChunkedTest
{
    public function getControllerString()
    {
        return 'Oneup\UploaderBundle\Controller\BlueimpController';
    }

    protected function getRequestMock()
    {
        $mock = $this->getMock('Symfony\Component\HttpFoundation\Request');
        
        $headers = $this->getMock('Symfony\Component\HttpFoundation\HeaderBag');
        $headers
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'headersGetCb')))
        ;
        
        $mock->headers = $headers;
        
        $mock->files = array(
            array($this->getUploadedFile())
        );
        
        return $mock;
    }

    public function containerGetCb($inp)
    {
        if($inp == 'session')
        {
            $mock = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
            $mock
                ->expects($this->any())
                ->method('getId')
                ->will($this->returnValue('fixed-id'))
            ;
            
            return $mock;
        }
        
        return parent::containerGetCb($inp);
    }
    
    public function headersGetCb($inp)
    {
        if($inp == 'content-disposition')
            return 'grumpy-cat.jpeg';
        
        if($inp == 'content-range')
        {
            if($this->currentChunk == ($this->numberOfChunks - 1))
            {
                return sprintf('- 9218, 10240/10241');
            }
            else
            {
                $size = 1024;
                $ret = sprintf('- %d, %d/%d', $this->currentChunk * $size, $this->currentChunk * $size + $size, 10240);
                
                return $ret;
            }
        }
    }
}
