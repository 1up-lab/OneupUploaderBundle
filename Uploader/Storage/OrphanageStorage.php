<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Filesystem as GaufretteFilesystem;

use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;
use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface;

class OrphanageStorage extends GaufretteStorage implements OrphanageStorageInterface
{
    protected $orphanage;
    protected $masked;
    protected $session;
    protected $config;
    protected $type;
    
    public function __construct(GaufretteFilesystem $orphanage, GaufretteFilesystem $filesystem, SessionInterface $session, $config, $type)
    {
        parent::__construct($orphanage);
        
        $this->orphanage = $orphanage;
        $this->masked = $filesystem;
        $this->session = $session;
        $this->config = $config;
        $this->type = $type;
    }
    
    public function upload(File $file, $name = null, $path = null)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');
        
        // generate a path based on session id
        $path = $this->getPath();
        
        return parent::upload($file, $name, $path);
    }
    
    public function uploadFiles($keep = false)
    {
        // switch orphanage with masked filesystem
        $this->filesystem = $this->masked;
        
        $uploaded = array();
        foreach($this->getFiles() as $file)
        {
            $uploaded[] = parent::upload(new File($this->getWrapper($file)), str_replace($this->getPath(), '', $file));
            
            if(!$keep)
            {
                $this->orphanage->delete($file);
            }
        }
        
        return $uploaded;
    }
    
    public function getFiles()
    {
        $keys = $this->orphanage->listKeys($this->getPath());
        
        return $keys['keys'];
    }
    
    protected function getPath()
    {
        $id = $this->session->getId();
        $path = sprintf('%s/%s/%s', $this->config['directory'], $id, $this->type);
        
        return $path;
    }
    
    protected function getWrapper($key)
    {
        return sprintf('gaufrette://%s/%s', $this->config['domain'], $key);
    }
}