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
    
    public function addFile(File $file)
    {
        // prefix directory with session id
        $id = $session->getId();
        $path = sprintf('%s/%s/%s', $this->config['directory'], $id, $file->getRealPath());
        
        var_dump($path);
        die();
    }
    
    public function removeFile(File $file)
    {
        
    }
    
    public function getFiles()
    {
        
    }
}