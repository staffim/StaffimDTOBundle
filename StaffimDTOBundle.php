<?php

namespace Staffim\DTOBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Staffim\DTOBundle\Filterer\DependencyInjection\CompilerPass\AnnotationFilterFactoryCompilerPass;
use Staffim\DTOBundle\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;

class StaffimDTOBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AnnotationFilterFactoryCompilerPass());
        $container->addCompilerPass(new EventDispatcherCompilerPass());
    }
}
