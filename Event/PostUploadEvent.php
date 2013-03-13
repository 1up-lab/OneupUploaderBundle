<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class PostUploadEvent extends Event
{
    protected $file;
    protected $request;
    
    public function __construct(File $file, Request $request)
    {
        $this->file = $file;
        $this->request = $request;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
}