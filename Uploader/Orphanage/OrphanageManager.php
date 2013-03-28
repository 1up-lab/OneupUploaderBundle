<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Symfony\Component\DependencyInjection\ContainerInterface;

class OrphanageManager
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function get($key)
    {
        return $this->container->get(sprintf('oneup_uploader.orphanage.%s', $key));
    }
}