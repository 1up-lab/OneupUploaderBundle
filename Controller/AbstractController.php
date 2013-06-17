<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

abstract class AbstractController
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
    
    abstract public function upload();

    /**
     *  This internal function handles the actual upload process
     *  and will most likely be called from the upload()
     *  function in the implemented Controller.
     *
     *  Note: The return value differs when
     *
     *  @param UploadedFile The file to upload
     *  @return File the actual file
     */
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
    
    /**
     *  This function is a helper function which dispatches post upload
     *  and post persist events.
     */
    protected function dispatchEvents($uploaded, ResponseInterface $response, Request $request)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        
        // dispatch post upload event (both the specific and the general)
        $postUploadEvent = new PostUploadEvent($uploaded, $response, $request, $this->type, $this->config);
        $dispatcher->dispatch(UploadEvents::POST_UPLOAD, $postUploadEvent);
        $dispatcher->dispatch(sprintf('%s.%s', UploadEvents::POST_UPLOAD, $this->type), $postUploadEvent);
        
        if(!$this->config['use_orphanage'])
        {
            // dispatch post persist event (both the specific and the general)
            $postPersistEvent = new PostPersistEvent($uploaded, $response, $request, $this->type, $this->config);
            $dispatcher->dispatch(UploadEvents::POST_PERSIST, $postPersistEvent);
            $dispatcher->dispatch(sprintf('%s.%s', UploadEvents::POST_PERSIST, $this->type), $postPersistEvent);
        }
    }

    protected function validate(UploadedFile $file)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $event = new ValidationEvent($file, $this->config, $this->type);
        
        try
        {
            $dispatcher->dispatch(UploadEvents::VALIDATION, $event);
        }
        catch(ValidationException $exception)
        {
            // pass the exception one level up
            throw new UploadException($exception->getMessage());
        }
    }
}
