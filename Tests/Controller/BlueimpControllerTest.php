<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Tests\Controller\AbstractControllerTest;

class BlueimpControllerTest extends AbstractControllerTest
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
            ->will($this->returnValue(null))
        ;
        
        $mock->headers = $headers;

        $mock->files = array(
            array($this->getUploadedFile())
        );
        
        return $mock;
    }
}