<?php

namespace Oneup\UploaderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ControllerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tags = $container->findTaggedServiceIds('oneup_uploader.routable');

        if(count($tags) > 0 && $container->hasDefinition('oneup_uploader.routing.loader'))
        {
            $loader = $container->getDefinition('oneup_uploader.routing.loader');

            foreach($tags as $id => $tag)
            {
                $loader->addMethodCall('addController', array($tag[0]['type'], $id));
            }
        }
    }
}