<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Controller\UploadControllerInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Chunk\ChunkManagerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $request;
    protected $namer;
    protected $storage;
    protected $config;
    protected $dispatcher;
    protected $type;
    protected $chunkManager;
    
    public function __construct(Request $request, NamerInterface $namer, StorageInterface $storage, EventDispatcherInterface $dispatcher, $type, array $config, ChunkManagerInterface $chunkManager)
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
            try
            {
                $ret = $totalParts > 1 ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
            }
            catch(UploadException $e)
            {
                // an error happended, return this error message.
                return new JsonResponse(array('error' => $e->getMessage()));
            }
        }
        
        return $ret;
    }
    
    protected function handleUpload(UploadedFile $file)
    {
        // check if the file size submited by the client is over the max size in our config
        if($file->getClientSize() > $this->config['max_size'])
            throw new UploadException('File is too large.');
        
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        // if this mapping defines at least one type of an allowed extension,
        // test if the current is in this array
        if(count($this->config['allowed_types']) > 0 && !in_array($extension, $this->config['allowed_types']))
            throw new UploadException('This extension is not allowed.');
        
        // check if the current extension is mentioned in the disallowed types
        // and if so, throw an exception
        if(count($this->config['disallowed_types']) > 0 && in_array($extension, $this->config['disallowed_types']))
            throw new UploadException('This extension is not allowed.');
        
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