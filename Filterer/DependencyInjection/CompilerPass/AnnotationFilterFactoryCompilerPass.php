<?php

namespace Staffim\DTOBundle\Filterer\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AnnotationFilterFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('filterer.annotation_filter_factory')) {
            return;
        }

        $definition = $container->findDefinition(
            'filterer.annotation_filter_factory'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'filterer.filter'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addFilter',
                    [new Reference($id), $attributes["alias"]]
                );
            }
        }
    }
}
