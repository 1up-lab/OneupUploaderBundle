<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageInterface;

class Orphanage implements OrphanageInterface
{
    protected $session;
    protected $config;
    
    public function __construct(SessionInterface $session, $config)
    {
        $this->session = $session;
        $this->config = $config;
    }
    
    public function addFile(File $file, $name)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');
        
        // move file to orphanage
        return $file->move($this->getPath(), $name);
    }
    
    public function removeFile(File $file)
    {
        
    }
    
    public function getFiles()
    {
        
    }
    
    protected function getPath()
    {
        $id = $this->session->getId();
        $path = sprintf('%s/%s', $this->config['directory'], $id);
        
        return $path;
    }
}