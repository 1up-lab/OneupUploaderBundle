<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

class PostChunkUploadEvent extends Event
{
    protected $chunk;
    protected $request;
    protected $type;
    protected $response;
    protected $config;
    protected $isLast;

    public function __construct($chunk, ResponseInterface $response, Request $request, $isLast, $type, array $config)
    {
        $this->chunk = $chunk;
        $this->request = $request;
        $this->response = $response;
        $this->isLast = $isLast;
        $this->type = $type;
        $this->config = $config;
    }

    public function getChunk()
    {
        return $this->chunk;
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

    public function getIsLast()
    {
        return $this->isLast;
    }

    public function isLast()
    {
        return $this->isLast;
    }
}
