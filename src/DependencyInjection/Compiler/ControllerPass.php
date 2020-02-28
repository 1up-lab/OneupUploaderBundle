<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Find autowired controllers
        $autowired_controllers = $container->findTaggedServiceIds('controller.service_arguments');

        // Find OneUp controllers
        $controllers = $container->findTaggedServiceIds('oneup_uploader.controller');
        foreach ($controllers as $id => $tags) {
            // Get fully qualified name of service
            $fqdn = $container->getDefinition($id)->getClass();
            if (isset($autowired_controllers[$fqdn])) {
                // Retrieve auto wired controller
                $autowired_definition = $container->getDefinition((string) $fqdn);

                // Retrieve arguments from OneUp controller
                $arguments = $container->getDefinition($id)->getArguments();

                // Add arguments to auto wired controller
                if (empty($autowired_definition->getArguments())) {
                    foreach ($arguments as $argument) {
                        $autowired_definition->addArgument($argument);
                    }
                }

                // Remove autowire
                if (method_exists($autowired_definition, 'setAutowired')) {
                    $autowired_definition->setAutowired(false);
                }
            }
        }
    }
}
