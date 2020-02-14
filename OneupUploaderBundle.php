<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle;

use Oneup\UploaderBundle\DependencyInjection\Compiler\ControllerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OneupUploaderBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ControllerPass());
    }
}
