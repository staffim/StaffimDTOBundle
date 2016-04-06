<?php

namespace Staffim\DTOBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Staffim\DTOBundle\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;
use Staffim\DTOBundle\Filterer\DependencyInjection\CompilerPass\AnnotationFilterFactoryCompilerPass;

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
