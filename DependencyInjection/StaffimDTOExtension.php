<?php
namespace Staffim\DTOBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Vyacheslav Salakhutdinov <megazoll@gmail.com>
 */
class StaffimDTOExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $xmlLoader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $xmlLoader->load('services.xml');

        if (isset($config['dto_namespace'])) {
            $container->setParameter('staffim_dto.dto.factory.namespace', $config['dto_namespace']);
        }
        $container->setParameter('staffim_dto.dto.factory.postfix', $config['dto_postfix']);
        $container->setParameter('staffim_dto.trigger_events', $config['trigger_events']);
    }
}
