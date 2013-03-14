<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PostDeleteEvent extends Event
{
    protected $requets;
    protected $uuid;
    protected $type;
    
    public function __construct(Request $request, $uuid, $type)
    {
        $this->request = $request;
        $this->uuid = $uuid;
        $this->type = $type;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function getType()
    {
        return $this->type;
    }
}