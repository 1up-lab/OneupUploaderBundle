<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ValidationEvent extends Event
{
    protected $file;
    protected $config;

    public function __construct(UploadedFile $file, array $config, $type)
    {
        $this->file = $file;
        $this->config = $config;
        $this->type = $type;
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
}
