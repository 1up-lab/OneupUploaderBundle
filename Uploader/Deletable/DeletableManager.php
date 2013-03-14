<?php

namespace Oneup\UploaderBundle\Uploader\Deletable;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Oneup\UploaderBundle\Uploader\Deletable\DeletableManagerInterface;

class DeletableManager implements DeletableManagerInterface
{
    protected $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function addFile($type, $uuid, $name)
    {
        $session = $this->session;
        $key = sprintf('oneup_uploader.deletable.%s', $type);
        
        // get bag
        $arr = $session->get($key, array());
        
        $arr[$uuid] = $name;
        
        // and reattach it to session
        $session->set($key, $arr);
        
        return true;
    }
    
    public function getFile($type, $uuid)
    {
        $session = $this->session;
        $key = sprintf('oneup_uploader.deletable.%s', $type);
        
        // get bag
        $arr = $session->get($key, array());
        
        if(!array_key_exists($uuid, $arr))
            throw new \InvalidArgumentException(sprintf('No file with the uuid "%s" found', $uuid));
        
        return $arr[$uuid];
    }
    
    public function removeFile($type, $uuid)
    {
        $session = $this->session;
        $key = sprintf('oneup_uploader.deletable.%s', $type);
        
        // get bag
        $arr = $session->get($key, array());
        
        if(!array_key_exists($uuid, $arr))
            throw new \InvalidArgumentException(sprintf('No file with the uuid "%s" found', $uuid));
        
        unset($arr[$uuid]);
        
        $session->set($key, $arr);
        
        return true;
    }
}