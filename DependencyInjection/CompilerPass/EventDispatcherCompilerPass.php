<?php

namespace Staffim\DTOBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventDispatcherCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('staffim_dto.trigger_events')) {
            $mapperDefinition = $container->getDefinition('staffim_dto.dto.mapper');
            $mapperDefinition->addArgument($container->findDefinition('event_dispatcher'));
        }
    }
}
