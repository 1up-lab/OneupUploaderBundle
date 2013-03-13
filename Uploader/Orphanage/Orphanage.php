<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageInterface;

class Orphanage implements OrphanageInterface
{
    protected $session;
    protected $storage;
    protected $namer;
    protected $config;
    protected $type;
    
    public function __construct(SessionInterface $session, StorageInterface $storage, $config, $type)
    {
        $this->session = $session;
        $this->storage = $storage;
        $this->config  = $config;
        $this->type    = $type;
    }
    
    public function addFile(File $file, $name)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');
        
        // move file to orphanage
        return $file->move($this->getPath(), $name);
    }
    
    public function uploadFiles($keep = false)
    {
        $system = new Filesystem();
        $finder = new Finder();
        
        if(!$system->exists($this->getPath()))
            return array();
        
        $finder->in($this->getPathRelativeToSession())->files();
        
        $uploaded = array();
        
        foreach($finder as $file)
        {
            $uploaded[] = $this->storage->upload($file);
            
            if(!$keep)
            {
                $system->remove($file);
            }
        }
        
        return $uploaded;
    }
    
    protected function getPath()
    {
        $id = $this->session->getId();
        $path = sprintf('%s/%s/%s', $this->config['directory'], $id, $this->type);
        
        return $path;
    }
    
    protected function getPathRelativeToSession()
    {
        $id = $this->session->getId();
        $path = sprintf('%s/%s', $this->config['directory'], $id);
        
        return $path;
    }
}