<?php

namespace Oneup\UploaderBundle\Event;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ValidationEvent extends Event
{
    protected $file;
    protected $config;
    protected $type;
    protected $request;
    protected $response;

    public function __construct(FileInterface $file, Request $request, array $config, $type, ResponseInterface $response = null)
    {
        $this->file = $file;
        $this->config = $config;
        $this->type = $type;
        $this->request = $request;
        $this->response = $response;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

}
