<?php

namespace Oneup\UploaderBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManagerInterface;

class OrphanageListener implements EventSubscriberInterface
{
    protected $manager;
    
    public function __construct(OrphanageManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public function add(PostUploadEvent $event)
    {
        $options = $event->getOptions();
        $request = $event->getRequest();
        $file = $event->getFile();
        $type = $event->getType();
        
        if(!array_key_exists('use_orphanage', $options) || !$options['use_orphanage'])
            return;
        
        $this->manager->get($type)->addFile($file, $options['file_name']);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => 'add',
        );
    }
}