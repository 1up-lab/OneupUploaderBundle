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
    
    public function add(PostUploadEvent $event)
    {
        $options = $event->getOptions();
        $request = $event->getRequest();
        $file = $event->getFile();
        
        if(!array_key_exists('use_orphanage', $options) || !$options['use_orphanage'])
            return;
        
        $this->orphanage->addFile($file, $options['file_name']);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => 'add',
        );
    }
}