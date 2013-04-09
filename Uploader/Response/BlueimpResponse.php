<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class BlueimpResponse extends AbstractResponse
{
    /**
     * This is an array containing elements of the following type
     * array(url, thumbnail_url, type, size, delete_url, delete_type)
     */
    protected $files;
    
    public function __construct()
    {
        $this->files = array();
        
        parent::__construct();
    }
    
    public function assemble()
    {
        $data = $this->data;
        $data['files'] = $this->files;
        
        return $data;
    }
    
    public function addFile($file)
    {
        $this->files[] = $file;
        
        return $this;
    }
    
    public function setFiles(array $files)
    {
        $this->files = $files;
        
        return $this;
    }
}