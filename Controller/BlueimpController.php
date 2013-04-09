<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Controller\UploadControllerInterface;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class BlueimpController implements UploadControllerInterface
{
    protected $container;
    protected $storage;
    protected $config;
    protected $type;
    
    public function __construct(ContainerInterface $container, StorageInterface $storage, array $config, $type)
    {
        $this->container = $container;
        $this->storage = $storage;
        $this->config = $config;
        $this->type = $type;
    }
    
    public function upload()
    {
        $request = $this->container->get('request');
        $dispatcher = $this->container->get('event_dispatcher');
        $translator = $this->container->get('translator');
        
        $response = new EmptyResponse();
        $files = $request->files;
        
        $chunked = !is_null($request->headers->get('content-range'));
        
        foreach($files as $file)
        {
            $file = $file[0];
            
            try
            {
                $uploaded = $chunked ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
        
                $postUploadEvent = new PostUploadEvent($uploaded, $response, $request, $this->type, $this->config);
                $dispatcher->dispatch(UploadEvents::POST_UPLOAD, $postUploadEvent);
            
                if(!$this->config['use_orphanage'])
                {
                    // dispatch post upload event
                    $postPersistEvent = new PostPersistEvent($uploaded, $response, $request, $this->type, $this->config);
                    $dispatcher->dispatch(UploadEvents::POST_PERSIST, $postPersistEvent);
                }
            }
            catch(UploadException $e)
            {
                $response->setSuccess(false);
                $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'));
                
                // an error happended, return this error message.
                return new JsonResponse(array());
            }
        }
        
        return new JsonResponse($response->assemble());
    }
    
    protected function handleUpload(UploadedFile $file)
    {
        $this->validate($file);
        
        // no error happend, proceed
        $namer = $this->container->get($this->config['namer']);
        $name  = $namer->name($file);
        
        // perform the real upload
        $uploaded = $this->storage->upload($file, $name);
        
        return $uploaded;
    }
    
    protected function handleChunkedUpload(UploadedFile $file)
    {
        $request = $this->container->get('request');
        $session = $this->container->get('session');
        $chunkManager = $this->container->get('oneup_uploader.chunk_manager');
        $headerRange = $request->headers->get('content-range');
        $attachmentName = rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $request->headers->get('content-disposition')));
        
        // split the header string to the appropriate parts
        list($tmp, $startByte, $endByte, $totalBytes) = preg_split('/[^0-9]+/', $headerRange);
        
        $uploaded = null;
        
        // getting information about chunks
        // note: We don't have a chance to get the last $total
        // correct. This is due to the fact that the $size variable
        // is incorrect. As it will always be a higher number than
        // the one before, we just let that happen, if you have
        // any idea to fix this without fetching information about
        // previously saved files, let me know.
        $size  = ($endByte + 1 - $startByte);
        $last  = ($endByte + 1) == $totalBytes;
        $index = $last ? \PHP_INT_MAX : floor($startByte / $size);
        $total = ceil($totalBytes / $size);
        
        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid  = md5(sprintf('%s.%s', $attachmentName, $session->getId()));
        $orig  = $attachmentName;
        
        $chunkManager->addChunk($uuid, $index, $file, $orig);
        
        // if all chunks collected and stored, proceed
        // with reassembling the parts
        if(($endByte + 1) == $totalBytes)
        {
            // we'll take the first chunk and append the others to it
            // this way we don't need another file in temporary space for assembling
            $chunks = $chunkManager->getChunks($uuid);
            
            // assemble parts
            $assembled = $chunkManager->assembleChunks($chunks);
            $path = $assembled->getPath();
            
            // create a temporary uploaded file to meet the interface restrictions
            $uploadedFile = new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, null, null, true);
            
            // validate this entity and upload on success
            $this->validate($uploadedFile);
            $uploaded = $this->handleUpload($uploadedFile);
            
            $chunkManager->cleanup($path);
        }
        
        return $uploaded;
    }
    
    protected function validate(UploadedFile $file)
    {
        // check if the file size submited by the client is over the max size in our config
        if($file->getClientSize() > $this->config['max_size'])
            throw new UploadException('error.maxsize');
        
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        // if this mapping defines at least one type of an allowed extension,
        // test if the current is in this array
        if(count($this->config['allowed_extensions']) > 0 && !in_array($extension, $this->config['allowed_extensions']))
            throw new UploadException('error.whitelist');
        
        // check if the current extension is mentioned in the disallowed types
        // and if so, throw an exception
        if(count($this->config['disallowed_extensions']) > 0 && in_array($extension, $this->config['disallowed_extensions']))
            throw new UploadException('error.blacklist');
        
    }
}