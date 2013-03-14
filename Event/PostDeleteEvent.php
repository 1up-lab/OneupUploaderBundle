<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\File;

class PostDeleteEvent extends Event
{
    protected $file;
    protected $request;
    protected $type;
    protected $options;
    
    public function __construct(File $file, Request $request, $type, array $options = array())
    {
        $this->file = $file;
        $this->request = $request;
        $this->type = $type;
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
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
}