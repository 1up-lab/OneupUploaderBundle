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
use Oneup\UploaderBundle\Uploader\Response\UploaderResponse;

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
        
        $response = new UploaderResponse();
        $files = $request->files;
        
        foreach($files as $file)
        {
            $file = $file[0];
            
            try
            {
                $uploaded = $this->handleUpload($file);
        
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
        
        return new JsonResponse(array('files' => array()));
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