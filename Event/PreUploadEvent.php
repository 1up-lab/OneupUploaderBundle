<?php

namespace Oneup\UploaderBundle\Event;

use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PreUploadEvent extends Event
{
    protected $file;
    protected $request;
    protected $type;
    protected $response;
    protected $config;

    public function __construct($file, ResponseInterface $response, Request $request, $type, array $config)
    {
        $this->file = $file;
        $this->request = $request;
        $this->response = $response;
        $this->type = $type;
        $this->config = $config;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
