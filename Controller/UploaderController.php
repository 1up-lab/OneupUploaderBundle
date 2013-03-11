<?php

namespace Oneup\UploaderBundle\Controller;

class UploaderController
{
    protected $mappings;
    
    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }
    
    public function upload($mapping)
    {
        
    }
}