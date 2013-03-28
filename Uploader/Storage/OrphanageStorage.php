<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Gaufrette\Filesystem as GaufretteFilesystem;

use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;
use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface;

class OrphanageStorage extends GaufretteStorage implements OrphanageStorageInterface
{
    protected $masked;
    protected $session;
    protected $config;
    protected $type;
    
    public function __construct(GaufretteFilesystem $orphanage, GaufretteFilesystem $filesystem, SessionInterface $session, $config, $type)
    {
        parent::__construct($orphanage);
        
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
        $system = new Filesystem();
        $finder = new Finder();
        
        // switch orphanage with masked filesystem
        $this->filesystem = $this->masked;
        
        if(!$system->exists($this->getPath()))
            return array();
        
        $finder->in($this->getPath())->files();
        
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
        $path = sprintf('%s/%s', $this->config['directory'], $id);
        
        return $path;
    }
}