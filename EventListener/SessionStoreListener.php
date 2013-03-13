<?php

namespace Oneup\UploaderBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\UploadEvents;

class SessionStoreListener implements EventSubscriberInterface
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function addToSession(PostUploadEvent $event)
    {
        $request = $event->getRequest();
        $file = $event->getFile();
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => 'addToSession',
        );
    }
}