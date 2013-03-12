<?php

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Controller\UploadControllerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $namer;
    protected $storage;
    
    public function __construct($request, $namer, $storage, $config)
    {
        $this->request = $request;
        $this->namer = $namer;
        $this->storage = $storage;
        $this->config = $config;
    }
    
    public function upload()
    {
        $totalParts = $this->request->get('qqtotalparts', 1);
        
        return $totalParts > 1 ? $this->handleChunkedUpload() : $this->handleUpload();
    }
    
    protected function handleUpload()
    {
        // get filebag from request
        $files = $this->request->files;
        
        var_dump(get_class($this->storage)); die();
        
        foreach($files as $file)
        {
        }
        
        die();
    }
    
    protected function handleChunkedUpload()
    {
        
    }
}