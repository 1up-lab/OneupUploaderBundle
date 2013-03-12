<?php

namespace Oneup\UploaderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Oneup\UploaderBundle\DependencyInjection\Compiler\ControllerCompilerPass;

class OneupUploaderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new ControllerCompilerPass);
    }
}