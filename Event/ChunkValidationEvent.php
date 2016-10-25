<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ChunkValidationEvent extends Event
{
    protected $file;
    protected $config;
    protected $type;
    protected $request;
    protected $maxSize;

    public function __construct(UploadedFile $file, Request $request, array $config, $type, $max_size)
    {
        $this->file    = $file;
        $this->config  = $config;
        $this->type    = $type;
        $this->request = $request;
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

    public function getChunkMaxSize()
    {
        return $this->maxSize;
    }
}
