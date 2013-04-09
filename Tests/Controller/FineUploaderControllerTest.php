<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

class FineUploaderControllerTest extends AbstractControllerTest
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
            ->with('qqtotalparts')
            ->will($this->returnValue(1))
        ;
        
        $mock->files = array(
            $this->getUploadedFile()
        );
        
        return $mock;
    }
}