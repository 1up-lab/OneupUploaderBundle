<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManagerInterface;

class OrphanageManager implements OrphanageManagerInterface
{
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
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
            $finder->in($this->configuration['directory'])->date('<=' . -1 * (int) $this->configuration['maxage'] . 'seconds');
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
}