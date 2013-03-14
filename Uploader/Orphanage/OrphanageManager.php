<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManagerInterface;

class OrphanageManager implements OrphanageManagerInterface
{
    protected $container;
    protected $configuration;
    protected $orphanages;
    
    public function __construct(ContainerInterface $container, array $configuration)
    {
        $this->container = $container;
        $this->configuration = $configuration;
        $this->orphanages = array();
    }
    
    public function warmup()
    {
        $fileSystem = new FileSystem();
        $fileSystem->mkdir($this->configuration['directory']);
    }
    
    public function clear()
    {
        $system = new Filesystem();
        $finder = new Finder();
        
        try
        {
            $finder->in($this->configuration['directory'])->date('<=' . -1 * (int) $this->configuration['maxage'] . 'seconds')->files();
        }
        catch(\InvalidArgumentException $e)
        {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }
        
        foreach($finder as $file)
        {
            $system->remove($file);
        }
    }
    
    public function get($type)
    {
        return $this->getImplementation($type);
    }
    
    public function getImplementation($type)
    {
        if(!array_key_exists($type, $this->orphanages))
            throw new \InvalidArgumentException(sprintf('No Orphanage implementation of type "%s" found.', $type));
        
        return $this->orphanages[$type];
    }
    
    public function addImplementation($type, OrphanageInterface $orphanage)
    {
        $this->orphanages[$type] = $orphanage;
    }
}