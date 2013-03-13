<?php

namespace Oneup\UploaderBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageInterface;

class OrphanageListener implements EventSubscriberInterface
{
    protected $orphanage;
    
    public function __construct(OrphanageInterface $orphanage)
    {
        $this->orphanage = $orphanage;
    }
    
    public function addToSession(PostUploadEvent $event)
    {
        $request = $event->getRequest();
        $file = $event->getFile();
        
        $this->orphanage->addFile($file);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => 'addToSession',
        );
    }
}