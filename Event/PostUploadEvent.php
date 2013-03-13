<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\File;

class PostUploadEvent extends Event
{
    protected $file;
    protected $request;
    
    public function __construct(File $file, Request $request, array $options = array())
    {
        $this->file = $file;
        $this->request = $request;
        $this->options = $options;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
}