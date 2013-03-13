<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Controller\UploadControllerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $namer;
    protected $storage;
    
    public function __construct($request, $namer, $storage, $config, $dispatcher)
    {
        $this->request = $request;
        $this->namer = $namer;
        $this->storage = $storage;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
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
            $name = $this->namer->name($file, $this->config['directory_prefix']);
            
            $postUploadEvent = new PostUploadEvent($file, $this->request, array(
                'use_orphanage' => $this->config['use_orphanage'],
                'file_name' => $name,
            ));
            $this->dispatcher->dispatch(UploadEvents::POST_UPLOAD, $postUploadEvent);
            
            if(!$this->config['use_orphanage'])
            {
                $uploaded = $this->storage->upload($file, $name);
            
                // dispatch post upload event
                $postPersistEvent = new PostPersistEvent($uploaded, $this->request);
                $this->dispatcher->dispatch(UploadEvents::POST_PERSIST, $postPersistEvent);
            }
        }
        
        return new JsonResponse(array('success' => true));
    }
    
    protected function handleChunkedUpload()
    {
        
    }
}