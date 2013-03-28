<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
use Oneup\UploaderBundle\Uploader\Response\UploaderResponse;

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
        $response = new UploaderResponse();
        $totalParts = $this->request->get('qqtotalparts', 1);
        $files = $this->request->files;
        $chunked = $totalParts > 1;
        
        foreach($files as $file)
        {
            try
            {
                $uploaded = $chunked ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
        
                $postUploadEvent = new PostUploadEvent($uploaded, $response, $this->request, $this->type, $this->config);
                $this->dispatcher->dispatch(UploadEvents::POST_UPLOAD, $postUploadEvent);
            
                if(!$this->config['use_orphanage'])
                {
                    // dispatch post upload event
                    $postPersistEvent = new PostPersistEvent($uploaded, $response, $this->request, $this->type, $this->config);
                    $this->dispatcher->dispatch(UploadEvents::POST_PERSIST, $postPersistEvent);
                }
            }
            catch(UploadException $e)
            {
                $response->setSuccess(false);
                
                // an error happended, return this error message.
                return new JsonResponse($response->assemble());
            }
        }
        
        return new JsonResponse($response->assemble());
    }
    
    protected function handleUpload(UploadedFile $file)
    {
        $this->validate($file);
        
        $name = $this->namer->name($file, $this->config['directory_prefix']);
        $uploaded = $this->storage->upload($file, $name);
        
        return $uploaded;
    }
    
    protected function handleChunkedUpload(UploadedFile $file)
    {
        $request  = $this->request;
        $uploaded = null;
        
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
            
            // create a temporary uploaded file to meet the interface restrictions
            $uploadedFile = new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, null, null, true);
            
            // validate this entity and upload on success
            $this->validate($uploadedFile);
            $uploaded = $this->handleUpload();
            
            $this->chunkManager->cleanup($path);
        }
        
        return $uploaded;
    }
    
    protected function validate(UploadedFile $file)
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
        
    }
}