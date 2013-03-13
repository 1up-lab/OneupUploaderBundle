<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
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
        
        foreach($files as $file)
        {
            // get name and directory and put them together
            $name = $this->namer->name($file);
            $name  = sprintf('%s/%s', $this->config['directory_prefix'], $name);
            
            $this->storage->upload($file, $name);
        }
        
        return new JsonResponse(array('success' => true));
    }
    
    protected function handleChunkedUpload()
    {
        
    }
}