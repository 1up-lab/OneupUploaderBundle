<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;

use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Controller\UploadControllerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $namer;
    protected $storage;
    
    public function __construct($request, $namer, $storage, $dispatcher, $type, $config, $chunkManager)
    {
        $this->request = $request;
        $this->namer = $namer;
        $this->storage = $storage;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
        $this->type = $type;
        $this->chunkManager = $chunkManager;
    }
    
    public function upload()
    {
        $totalParts = $this->request->get('qqtotalparts', 1);
        $files = $this->request->files;
        
        foreach($files as $file)
        {
            $ret = $totalParts > 1 ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
        }
        
        return $ret;
    }
    
    protected function handleUpload(UploadedFile $file)
    {
        $name = $this->namer->name($file, $this->config['directory_prefix']);
            
        $postUploadEvent = new PostUploadEvent($file, $this->request, $this->type, array(
            'use_orphanage' => $this->config['use_orphanage'],
            'file_name' => $name,
        ));
        $this->dispatcher->dispatch(UploadEvents::POST_UPLOAD, $postUploadEvent);
            
        if(!$this->config['use_orphanage'])
        {
            $uploaded = $this->storage->upload($file, $name);
            
            // dispatch post upload event
            $postPersistEvent = new PostPersistEvent($uploaded, $this->request, $this->type);
            $this->dispatcher->dispatch(UploadEvents::POST_PERSIST, $postPersistEvent);
        }
        
        return new JsonResponse(array('success' => true));
    }
    
    protected function handleChunkedUpload(UploadedFile $file)
    {
        $request = $this->request;
        
        // getting information about chunks
        $index = $request->get('qqpartindex');
        $total = $request->get('qqtotalparts');
        $uuid  = $request->get('qquuid');
        $orig  = $request->get('qqfilename');
            
        $this->chunkManager->addChunk($uuid, $index, $file, $orig);
        
        // if all chunks collected and stored, proceed
        // with reassembling the parts
        if(($total - 1) == $index)
        {
            // we'll take the first chunk and append the others to it
            // this way we don't need another file in temporary space for assembling
            $chunks = $this->chunkManager->getChunks($uuid);
                
            // assemble parts
            $assembled = $this->chunkManager->assembleChunks($chunks);
            $path = $assembled->getPath();
            
            $ret = $this->handleUpload(new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, null, null, true));
            
            $this->chunkManager->cleanup($path);
        }
        
        return new JsonResponse(array('success' => true));
    }
}