<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class PostPersistEvent extends Event
{
    protected $file;
    protected $request;
    protected $type;
    
    public function __construct(File $file, Request $request, $type)
    {
        $this->file = $file;
        $this->request = $request;
        $this->type = $type;
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
}