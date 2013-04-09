<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class FineUploaderResponse extends AbstractResponse
{
    protected $files;
    
    public function __construct()
    {
        $this->success = array();
        
        parent::__construct();
    }
    
    public function assemble()
    {
        // explicitly overwrite success and error key
        // as these keys are used internaly by the
        // frontend uploader
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