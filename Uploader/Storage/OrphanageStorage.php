<?php

namespace Oneup\UploaderBundle\Storage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Gaufrette\Filesystem;

use Oneup\UploaderBundle\Storage\GaufretteStorage;

class OrphanageStorage extends GaufretteStorage implements OrphanageStorageInterface
{
    protected $masked;
    protected $session;
    protected $config;
    protected $type;
    
    public function __construct(Gaufrette $orphanage, Gaufrette $filesystem, SessionInterface $session, $config, $type)
    {
        parent::__construct($orphanage);
        
        $this->masked = $filesystem;
        $this->session = $session;
        $this->config = $config;
        $this->type = $type;
    }
    
    public function upload(File $file, $name = null)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');
        
        parent::upload($file, $name);
    }
    
    public function uploadFiles($keep = false)
    {
        $system = new Filesystem();
        $finder = new Finder();
        
        // switch orphanage with masked filesystem
        $this->filesystem = $this->masked;
        
        if(!$system->exists($this->getPath()))
            return array();
        
        $finder->in($this->getPathRelativeToSession())->files();
        
        $uploaded = array();
        foreach($finder as $file)
        {
            $uploaded[] = $this->upload(new UploadedFile($file->getPathname(), $file->getBasename(), null, null, null, true));
            
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