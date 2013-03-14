<?php

namespace Oneup\UploaderBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\UploadEvents;
use Oneup\UploaderBundle\Uploader\Deletable\DeletableManagerInterface;

class DeletableListener implements EventSubscriberInterface
{
    protected $manager;
    
    public function __construct(DeletableManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public function register(PostUploadEvent $event)
    {
        $options = $event->getOptions();
        $request = $event->getRequest();
        $file = $event->getFile();
        $type = $event->getType();
        
        if(!array_key_exists('deletable', $options) || !$options['deletable'])
            return;
        
        if(!array_key_exists('file_name', $options))
            return;
        
        $uuid = $request->get('qquuid');
        
        $this->manager->addFile($type, $uuid, $options['file_name']);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => 'register',
        );
    }
}
